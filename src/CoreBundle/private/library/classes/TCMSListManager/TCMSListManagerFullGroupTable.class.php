<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CmsBackendBundle\BackendSession\BackendSessionInterface;
use ChameleonSystem\CoreBundle\Field\Provider\ClassFromTableFieldProviderInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsPermissionAttributeConstants;
use Doctrine\DBAL\Connection;

require_once PATH_LIBRARY.'/classes/TCMSListManager/tcms_functionblock_callback.fun.php';
require_once PATH_LIBRARY.'/classes/TCMSListManager/LoadCallbackLibrary.inc.php';

/**
 * Uses the TFullGroupTable to manage the list.
 *
 * Saves the current table object in the `_listObjCache` session variable in order to
 * support stateful lists (e.g. for sorting). {@see getTableFromSessionCache} {@see saveTableInSessionCache}
 */
class TCMSListManagerFullGroupTable extends TCMSListManager
{
    /**
     * holds the full group table object.
     *
     * @var TFullGroupTable
     */
    public $tableObj;

    /**
     * Number of columns.
     *
     * @var int
     */
    public $columnCount = 0;

    /**
     * {@inheritdoc}
     */
    public function Init($oTableConf)
    {
        parent::Init($oTableConf);

        $this->tableObj = null;
        $this->columnCount = 0;
        $_SESSION['_tmpCurrentTableName'] = $this->oTableConf->sqlData['name']; // needed for the callback functions...
        $_SESSION['_tmpCurrentTableID'] = $this->oTableConf->sqlData['id']; // needed for the callback functions...

        $listCacheKey = $this->GetListCacheKey();
        $cachedTableObj = null;
        if (true === $this->isTableCachingEnabled()) {
            $cachedTableObj = $this->getTableFromSessionCache($listCacheKey);
        }

        // table is not in cache, load it
        if (null === $cachedTableObj) {
            $this->CreateTableObj(); // also calls PostCreateTableObjectHook();
            $this->tableObj->orderList = [];
            $this->AddFields();
            $this->AddSortInformation();
            $this->AddTableGrouping();
        } else { // table is in cache, load it from there and inject current post parameters
            $oGlobal = TGlobal::instance();
            $postData = $oGlobal->GetUserData();
            $this->tableObj = $cachedTableObj;
            $this->tableObj->_postData = array_merge($this->tableObj->_postData, $postData); // overwrite anything that is passed via get or post
            foreach ($this->tableObj->customSearchFieldParameter as $key => $val) {
                if (!array_key_exists($key, $this->tableObj->_postData)) {
                    $this->tableObj->_postData[$key] = $this->tableObj->customSearchFieldParameter[$key];
                }
            }
            $this->AddSortInformation();
            if (isset($this->tableObj->groupByCell->colSpan)) {
                $this->AddTableGrouping($this->tableObj->groupByCell->colSpan);
            }
            $this->tableObj->sql = $this->FilterQuery(); // need to refresh this since it may change for mlt lists
            if (isset($postData['_startRecord']) && (!empty($postData['_startRecord']) || '0' == $postData['_startRecord'])) { // we need to check the 0 condition because 0 is treated as empty
                $this->tableObj->startRecord = $postData['_startRecord']; // set current start record
            }

            $this->PostCreateTableObjectHook();
        }

        if (true === $this->isTableCachingEnabled() && false === $this->isModuleFunction() && (true === $this->isTableCacheChangeRequest() || null === $cachedTableObj)) {
            $this->saveTableInSessionCache($listCacheKey, $this->tableObj);
        }
    }

    private function isTableCachingEnabled(): bool
    {
        return $this->bListCacheEnabled && CMS_ACTIVE_BACKEND_LIST_CACHE;
    }

    private function getTableFromSessionCache(string $cacheKey): ?TFullGroupTable
    {
        if (false === \array_key_exists('_listObjCache', $_SESSION)) {
            return null;
        }

        $isObjectInSession = \array_key_exists($cacheKey, $_SESSION['_listObjCache']);

        if (false === $isObjectInSession) {
            return null;
        }

        $tmp = base64_decode($_SESSION['_listObjCache'][$cacheKey]);

        return unserialize(gzuncompress($tmp));
    }

    private function saveTableInSessionCache(string $cacheKey, TFullGroupTable $table): void
    {
        if (false === \array_key_exists('_listObjCache', $_SESSION)) {
            $_SESSION['_listObjCache'] = [];
        }

        $tmp = serialize($table);
        $tmp = gzcompress($tmp, 9);
        $_SESSION['_listObjCache'][$cacheKey] = base64_encode($tmp);
    }

    public static function clearTableCache(): void
    {
        unset($_SESSION['_listObjCache']);
    }

    private function isTableCacheChangeRequest(): bool
    {
        $inputFilter = $this->getInputFilterUtil();

        $listName = $inputFilter->getFilteredInput('_listName');

        if (null === $listName) {
            return false;
        }

        return $listName === 'cmstablelistObj'.$this->oTableConf->sqlData['cmsident'];
    }

    private function isModuleFunction(): bool
    {
        $inputFilter = $this->getInputFilterUtil();

        return null !== $inputFilter->getFilteredInput('_fnc');
    }

    protected function isRecordEditPossible(): bool
    {
        return null === $this->_GetRecordClickJavaScriptFunctionName();
    }

    /**
     * @return Symfony\Component\HttpFoundation\Request
     */
    protected function getRequest()
    {
        return ServiceLocator::get('request_stack')->getCurrentRequest();
    }

    /**
     * returns a key that combines the different cache parameters to a unique key.
     *
     * @return string
     */
    public function GetListCacheKey()
    {
        return TCacheManagerRuntimeCache::GetKey($this->GetCacheParameters());
    }

    /**
     * returns the cache parameters needed for identification of the right cache object.
     *
     * @return array
     */
    protected function GetCacheParameters()
    {
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
        /** @var BackendSessionInterface $backendSession */
        $backendSession = ServiceLocator::get('chameleon_system_cms_backend.backend_session');

        $request = $this->getRequest();

        $aCacheParameters = ['class' => get_class($this),
            'table' => $this->oTableConf->sqlData['name'],
            'userid' => $securityHelper->getUser()?->getId(),
            'sRestriction' => $this->sRestriction,
            'sRestrictionField' => $this->sRestrictionField,
            'field' => $request->get('field'),
            'pagedef' => $request->get('pagedef'),
            'sCurrentEditLanguageIsoCode' => $backendSession->getCurrentEditLanguageIso6391(),
        ];

        return $aCacheParameters;
    }

    /**
     * returns the table as a HTML String.
     *
     * @return string
     */
    public function GetList()
    {
        $table = '';
        $table .= $this->tableObj->Display();

        return $table;
    }

    /**
     * add table-specific buttons to the editor (add them directly to $this->oMenuItems).
     */
    protected function GetCustomMenuItems()
    {
        parent::GetCustomMenuItems();
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

        $tableInUserGroup = $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_ACCESS, $this->oTableConf->fieldName);
        if ($tableInUserGroup) {
            $portalIds = $securityHelper->getUser()?->getPortals();
            if (null === $portalIds) {
                $portalIds = [];
            }
            $portalRestriction = implode(', ', array_map(fn ($id) => $this->getDatabaseConnection()->quote($id), array_keys($portalIds)));
            /* Check for Export Profiles */
            if (!empty($portalRestriction)) {
                $query = "SELECT EXISTS (SELECT 1 FROM `cms_export_profiles` WHERE `cms_tbl_conf_id` = :tableId AND `cms_portal_id` IN ({$portalRestriction}))";
                $hasExportProfile = $this->getDatabaseConnection()->fetchOne($query, ['tableId' => $this->oTableConf->sqlData['id']]);
                if (1 === (int) $hasExportProfile) {
                    $oMenuItem = new TCMSTableEditorMenuItem();
                    $oMenuItem->sDisplayName = ServiceLocator::get('translator')->trans('chameleon_system_core.action.export');
                    $oMenuItem->sIcon = 'far fa-save';

                    $sListClass = 'TCMSListManager';

                    $oListClass = $this->oTableConf->GetLookup('cms_tbl_list_class_id');
                    if (isset($oListClass) && !is_null($oListClass) && is_object($oListClass)) {
                        $sListClass = $oListClass->sqlData['classname'];
                    }

                    $aParameters = [
                        'pagedef' => 'CMSTableExport',
                        '_pagedefType' => 'Core',
                        'tableID' => $this->oTableConf->id,
                        'tableCmsIdentID' => $this->oTableConf->sqlData['cmsident'],
                        'listClass' => $sListClass,
                        'listCacheKey' => $this->GetListCacheKey(),
                    ];

                    $js = "CreateModalIFrameDialogCloseButton('".PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURL($aParameters)."',0,0,'".TGlobal::OutJS(ServiceLocator::get('translator')->trans('chameleon_system_core.action.export'))."');";

                    $oMenuItem->sOnClick = $js;
                    $this->oMenuItems->AddItem($oMenuItem);
                }
            }

            if ('cms_field_conf' === $this->oTableConf->fieldName) { // CMS table fields ("Record fields")
                if (true === $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_EDIT, $this->oTableConf->sqlData['name'])) {
                    $sFormName = 'cmstablelistObj' . $this->oTableConf->sqlData['cmsident'];
                    $oMenuItem = new TCMSTableEditorMenuItem();
                    $oMenuItem->sItemKey = 'add_selected_as_list_fields';
                    $oMenuItem->setTitle(ServiceLocator::get('translator')->trans('chameleon_system_core.list.add_selected_as_list_fields'));

                    $oMenuItem->sIcon = 'fas fa-list-alt'; // fas fa-sort
                    $oMenuItem->setButtonStyle('btn-info');
                    $oMenuItem->sOnClick = "addSelectedAsListFields('{$sFormName}');";
                    $this->oMenuItems->AddItem($oMenuItem);
                }

                if (true === $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_EDIT, $this->oTableConf->sqlData['name'])) {
                    $sFormName = 'cmstablelistObj' . $this->oTableConf->sqlData['cmsident'];
                    $oMenuItem = new TCMSTableEditorMenuItem();
                    $oMenuItem->sItemKey = 'add_selected_as_sort_fields';
                    $oMenuItem->setTitle(ServiceLocator::get('translator')->trans('chameleon_system_core.list.add_selected_as_sort_fields'));

                    $oMenuItem->sIcon = 'fas fa-sort';
                    $oMenuItem->setButtonStyle('btn-info');
                    $oMenuItem->sOnClick = "addSelectedAsSortFields('{$sFormName}');";
                    $this->oMenuItems->AddItem($oMenuItem);
                }
            }

            if ($securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_DELETE, $this->oTableConf->sqlData['name'])) {
                $sFormName = 'cmstablelistObj'.$this->oTableConf->sqlData['cmsident'];
                $oMenuItem = new TCMSTableEditorMenuItem();
                $oMenuItem->sItemKey = 'deleteall';
                $oMenuItem->sDisplayName = ServiceLocator::get('translator')->trans('chameleon_system_core.list.delete_selected');
                $oMenuItem->sIcon = 'far fa-trash-alt';
                $oMenuItem->setButtonStyle('btn-danger');
                $oMenuItem->sOnClick = "DeleteSelectedRecords('{$sFormName}');";
                $this->oMenuItems->AddItem($oMenuItem);
            }
        }
    }

    /**
     * generates the tableobject assumes that all parameters are in post.
     */
    public function CreateTableObj()
    {
        $oGlobal = TGlobal::instance();
        /** @var BackendSessionInterface $backendSession */
        $backendSession = ServiceLocator::get('chameleon_system_cms_backend.backend_session');

        $postData = $oGlobal->GetUserData();
        $this->tableObj = new TFullGroupTable();
        $this->tableObj->Init($postData);
        $this->tableObj->setLanguageId($backendSession->getCurrentEditLanguageId());
        $this->tableObj->sTableName = $this->oTableConf->sqlData['name'];
        $this->tableObj->orderList = [];
        $this->tableObj->formActionType = 'GET';
        $this->tableObj->listName = 'cmstablelistObj'.$this->oTableConf->sqlData['cmsident'];
        $this->tableObj->onClick = $this->_GetRecordClickJavaScriptFunctionName();
        $this->tableObj->showSearchBox = true;

        $this->tableObj->sql = $this->FilterQuery();
        $this->tableObj->hasCondition = true; // indicates whether the sql has a WHERE condition (default = false)
        $oCMSConfig = TdbCmsConfig::GetInstance();
        $this->tableObj->showRecordCount = $oCMSConfig->sqlData['entry_per_page'];

        // style of every field
        $this->tableObj->style->group = 'bg-secondary';
        $this->tableObj->style->groupSpacer = 'groupSpacer';
        $this->tableObj->style->header = 'bg-primary text-white';
        $this->tableObj->style->navigation = 'tblNav';
        $this->tableObj->style->filter = 'tblfilter';
        $this->tableObj->style->search = 'tblsearch';
        $this->tableObj->style->notFoundText = 'error';
        $this->tableObj->style->groupSelector = 'tblGroupSelector';
        $this->tableObj->style->searchButtonTDstyle = 'tblSearchButtonTDstyle';
        $this->tableObj->style->searchFieldTDstyle = 'tblSearchFieldTDstyle';

        $this->tableObj->hitText = ServiceLocator::get('translator')->trans('chameleon_system_core.list.current_page_details');
        $this->tableObj->searchFieldText = ServiceLocator::get('translator')->trans('chameleon_system_core.list.search_term');
        $this->tableObj->searchButtonText = ServiceLocator::get('translator')->trans('chameleon_system_core.list.perform_search');
        $this->tableObj->notFoundText = ServiceLocator::get('translator')->trans('chameleon_system_core.list.no_entries');
        $this->tableObj->pageingLocation = 'bottom';

        $this->tableObj->_postData = $postData;
        if (isset($postData['_startRecord']) && (!empty($postData['_startRecord']) || '0' == $postData['_startRecord'])) { // we need to check the 0 condition because 0 is treated as empty
            $this->tableObj->startRecord = $postData['_startRecord']; // set current start record
        }

        $this->tableObj->showRowsPerPageChooser = true;
        $this->AddRowCallback();

        $this->PostCreateTableObjectHook();
    }

    protected function AddRowCallback()
    {
        $this->tableObj->rowCallback = [$this, 'CallBackRowStyling'];
    }

    /**
     * returns the name of the javascript function to be called when the user clicks on a
     * record within the table.
     *
     * @return string
     */
    protected function _GetRecordClickJavaScriptFunctionName()
    {
        // if current edit language differs from record language we need to go to the language record or copy the record to the current language
        return null;
    }

    protected function AllowSortForAllStandardFields()
    {
        return true;
    }

    /**
     * Retrieves the list field configuration for all fields for a given table configuration id.
     *
     * @param int $tableConfigurationId Table configuration id
     *
     * @return array|null Field configuration records
     */
    public function getDisplayListFieldsConfig($tableConfigurationId)
    {
        $connection = $this->getDatabaseConnection();
        $query = 'SELECT * FROM `cms_tbl_display_list_fields`
                   WHERE `cms_tbl_conf_id` = :id
                ORDER BY `position`
               ';

        return $connection->fetchAllAssociative($query, [
            'id' => $tableConfigurationId,
        ]);
    }

    /**
     * Retrieves the list field configuration for a single field in a given table configuration by id.
     *
     * @param int $tableConfigurationId Table configuration id
     * @param string $field Field query string (e.g. `cms_tbl_conf`.`translation`)
     *
     * @return array|null Field configuration records
     */
    public function getDisplayListFieldConfig($tableConfigurationId, $field)
    {
        $connection = $this->getDatabaseConnection();
        $query = 'SELECT * FROM `cms_tbl_display_list_fields`
                   WHERE `cms_tbl_conf_id` = :id
                     AND `name` = :field
                ORDER BY `position`
               ';
        try {
            return $connection->fetchAllAssociative($query, [
                'id' => $tableConfigurationId,
                'field' => $field,
            ]);
        } catch (DBALException $e) {
            return null;
        }
    }

    /**
     * adds the field information of the table obj.
     */
    public function AddFields()
    {
        $this->AddRowPrefixFields();
        $allowSort = $this->AllowSortForAllStandardFields();
        $jsParas = $this->_GetRecordClickJavaScriptParameters();

        $listFieldsConfig = $this->getDisplayListFieldsConfig($this->oTableConf->id);

        $this->tableObj->searchFields = [];

        // add locking column if locking is active
        if ('1' == $this->oTableConf->sqlData['locking_active']) {
            $this->tableObj->AddHeaderField(['Locking' => ServiceLocator::get('translator')->trans('chameleon_system_core.list.column_name_lock')], 'left', null, 1, false, 30);
            $this->tableObj->AddColumn('id', 'left', [$this, 'CallBackLockingStatus'], $jsParas, 1);
            ++$this->columnCount;
        }

        $this->_AddFunctionColumn();

        if (count($listFieldsConfig) > 0) {
            foreach ($listFieldsConfig as $fieldConfig) {
                $fieldConfig = $this->TransformFieldForTranslations($fieldConfig);
                $dbfieldname = trim($fieldConfig['name']);
                $originalField = trim($fieldConfig['db_alias']);
                $originalTable = null;

                $tableFieldDictionary = $this->getClassFromTableFieldProvider()->getDictionaryFromTableField($dbfieldname);
                if (null !== $tableFieldDictionary) {
                    $originalTable = $tableFieldDictionary['tableName'];
                }

                ++$this->columnCount;
                if ($fieldConfig['width'] < 1) {
                    $fieldConfig['width'] = false;
                }
                $sTranslatedField = $fieldConfig['title'];
                $this->tableObj->AddHeaderField([$fieldConfig['name'] => $sTranslatedField], $fieldConfig['align'], null, 1, $allowSort, $fieldConfig['width']);
                $db_alias = trim($fieldConfig['db_alias']);
                $columnField = $dbfieldname;
                if ('' !== $db_alias) {
                    $columnField = [$db_alias => $dbfieldname];
                }
                $this->tableObj->AddColumn($columnField, $fieldConfig['align'], $this->getCellFormattingFunctionConfig($fieldConfig), $jsParas, 1, null, null, null, $originalField, $originalTable);
                if ($this->isListFieldSearchable($fieldConfig)) {
                    $this->tableObj->searchFields[$fieldConfig['name']] = 'full';
                }
            }
        } else {
            $this->columnCount += 3;
            $name = $this->oTableConf->GetNameColumn();
            $callback = $this->oTableConf->GetNameFieldCallbackFunction();
            $this->tableObj->AddHeaderField(['id' => 'ID'], 'left', null, 1, $allowSort, false);
            $sTranslatedField = ServiceLocator::get('translator')->trans('chameleon_system_core.list.column_name_cmsident');
            $this->tableObj->AddHeaderField(['cmsident' => $sTranslatedField], 'left', null, 1, $allowSort, false);
            $sTranslatedField = ServiceLocator::get('translator')->trans('chameleon_system_core.list.column_name_name');
            $this->tableObj->AddHeaderField([$name => $sTranslatedField], 'left', null, 1, $allowSort);

            $this->tableObj->AddColumn('id', 'left', ['TCMSRecord', 'callBackUuid'], $jsParas, 1);
            $this->tableObj->AddColumn('cmsident', 'left', null, $jsParas, 1);
            $originalField = $name;
            $aNameColumnData = $this->TransformFieldForTranslations(['db_alias' => $name, 'name' => $name]);
            $dbfieldname = trim($aNameColumnData['name']);
            $db_alias = trim($aNameColumnData['db_alias']);
            $columnField = $dbfieldname;
            if (!empty($db_alias)) {
                $columnField = [$db_alias => $dbfieldname];
            }

            $this->tableObj->AddColumn($columnField, 'left', $callback, $jsParas, 1, null, 'title', null, $originalField);

            $connection = $this->getDatabaseConnection();
            $quotedTableName = $connection->quoteIdentifier($this->oTableConf->sqlData['name']);
            $quotedName = $connection->quoteIdentifier($name);

            $this->tableObj->searchFields["$quotedTableName.`id`"] = 'right';
            $this->tableObj->searchFields["$quotedTableName.`cmsident`"] = 'none';
            $this->tableObj->searchFields["$quotedTableName.$quotedName"] = 'full';
        }

        $this->AddCustomColumns();
    }

    /**
     * Determines if a specified list field is searchable.
     *
     * This method evaluates if a field is searchable by checking if it does not end with '_mlt' (indicating it's not a multi-link-table/one to many field)
     * and verifying the field's existence in the database. It handles fields that may be aliases or virtual fields that only work with a callback method.
     */
    protected function isListFieldSearchable(array $fieldConfig): bool
    {
        $toolsService = $this->getToolsService();
        $cleanSqlName = trim(str_replace('`', '', $fieldConfig['name']));
        $tableName = $this->oTableConf->table;
        $fieldName = $cleanSqlName;

        if (str_contains($cleanSqlName, '.')) {
            [$tableName, $fieldName] = explode('.', $cleanSqlName);
        } elseif ($fieldConfig['cms_tbl_conf_id'] !== $this->oTableConf->id) {
            $tblConf = TdbCmsTblConf::GetNewInstance();
            if ($tblConf->Load($fieldConfig['cms_tbl_conf_id'])) {
                $tableName = $tblConf->sqlData['name'];
            } else {
                return false;
            }
        }

        return !str_ends_with($fieldName, '_mlt') && $toolsService::FieldExists($tableName, $fieldName, false);
    }

    /**
     * @return array|string|null - returns the types TGroupTable::AddColumn allows for the callback parameter: null, array('object','callbackName'), callbackNameString
     */
    protected function getCellFormattingFunctionConfig(array $cellConfig)
    {
        $formattingFunctionName = $cellConfig['callback_fnc'];

        if (true === $this->isLegacyFormattingFunction($formattingFunctionName)) {
            return $formattingFunctionName;
        }

        /**
         * @var TCMSRecord $recordObject
         */
        $recordObjectName = TCMSTableToClass::GetClassName('Tdb', $this->oTableConf->sqlData['name']);
        $recordObject = new $recordObjectName();

        return $recordObject->getCellFormattingFunction($cellConfig, $formattingFunctionName);
    }

    protected function isLegacyFormattingFunction(string $formatFunctionName): bool
    {
        if ('gcf_' === substr($formatFunctionName, 0, 4) || 'ccf_' === substr($formatFunctionName, 0, 4)) {
            return true;
        }

        return false;
    }

    /**
     * use this method to add field columns between the standard columns and the function column.
     */
    protected function AddCustomColumns()
    {
    }

    /**
     * if field based translation is active, then this will change the data in aField to
     * match the current language.
     *
     * @param array $aField - array of a list field (cms_tbl_display_list_fields -> name and db_alias are relevant)
     *
     * @return array
     */
    protected function TransformFieldForTranslations($aField)
    {
        $aField = TTools::TransformFieldForTranslations($aField, $this->oTableConf);

        return $aField;
    }

    protected function _GetRecordClickJavaScriptParameters()
    {
        $jsParas = ['id'];

        return $jsParas;
    }

    public function _AddFunctionColumn()
    {
        ++$this->columnCount;
        $sTranslatedField = ServiceLocator::get('translator')->trans('chameleon_system_core.list.column_name_actions');
        $this->tableObj->AddHeaderField(['id' => $sTranslatedField.'&nbsp;&nbsp;'], 'right', null, 1, false, false);
        $this->tableObj->AddColumn('id', 'left', [$this, 'CallBackFunctionBlock'], null, 1);
    }

    protected function AddRowPrefixFields()
    {
        ++$this->columnCount;
        $sFormName = 'cmstablelistObj'.$this->oTableConf->sqlData['cmsident'];
        $this->tableObj->AddHeaderField(['id' => '<input onclick="ChangeListMarking(this.checked,\''.$sFormName.'\');" type="checkbox" name="aInputIdList[checkall]" value="true" />'], 'center', null, 1, false, 10);
        $this->tableObj->AddColumn('id', 'center', [$this, 'CallBackDrawListItemSelectbox'], null, 1);
    }

    /**
     * @param string|int $columnCount
     */
    public function AddTableGrouping($columnCount = '')
    {
        if (false === \property_exists($this->oTableConf, 'fieldListGroupField')) {
            return;
        }
        $groupField = trim($this->oTableConf->fieldListGroupField);
        if ('' === $groupField) {
            return;
        }

        $fieldData = $this->getClassFromTableFieldProvider()->getDictionaryFromTableField($groupField);
        if (null !== $fieldData) {
            $databaseConnection = $this->getDatabaseConnection();
            $fieldName = $this->getFieldTranslationUtil()->getTranslatedFieldName($fieldData['tableName'], $fieldData['fieldName']);
            $quotedTableName = $databaseConnection->quoteIdentifier($fieldData['tableName']);
            $quotedFieldName = $databaseConnection->quoteIdentifier($fieldName);
            $groupField = "$quotedTableName.$quotedFieldName";
        }

        $list_group_field_column = trim($this->oTableConf->fieldListGroupFieldColumn);
        if (empty($columnCount)) {
            $columnCount = $this->columnCount;
        }
        $this->tableObj->AddGroupField([$list_group_field_column => $groupField], 'left', null, null, $columnCount);
        $this->tableObj->showGroupSelectorText = $this->oTableConf->fieldListGroupFieldHeader;
        $this->tableObj->showAllGroupsText = '['. ServiceLocator::get('translator')->trans('chameleon_system_core.list.group_show_all').']';
        $tmpArray = [$list_group_field_column => 'ASC'];
        $this->tableObj->orderList = array_merge($tmpArray, $this->tableObj->orderList);
    }

    /**
     * Adds / modifies the `orderList` of the table object. This method relies on the fact, that
     * the table object (including the `orderList`) is saved in the session {@see saveTableInSessionCache}
     * in order to build complex filters.
     *
     * The `_sort_order` GET parameter is being used in order to cycle through different sorting
     * modes for the property used as value. The modes being cycled are: ascending, descending, no sorting.
     *
     * If there is no object currently cached in the session, then the sorting is initialized to its
     * defaults based on the table configuration.
     *
     * NOTE: The reliance on the session cache means that cycling through the modes and having multiple
     *       sorted fields will not work correctly, if the session cache is disabled {@see isTableCachingEnabled}
     *       as the current sorting state is not persisted in the session.
     *
     * @return void
     */
    public function AddSortInformation()
    {
        $oGlobal = TGlobal::instance();
        $postdata = $oGlobal->GetUserData();

        $sListCacheKey = $this->GetListCacheKey();
        if (!array_key_exists('_listObjCache', $_SESSION)) {
            $_SESSION['_listObjCache'] = [];
        }
        $objectInSession = array_key_exists($sListCacheKey, $_SESSION['_listObjCache']);

        $objectChangeRequest = (isset($postdata['_listName']) && $postdata['_listName'] == 'cmstablelistObj'.$this->oTableConf->sqlData['cmsident']);
        if (array_key_exists('_sort_order', $postdata) && !empty($postdata['_sort_order']) && $objectChangeRequest) {
            // Toggling of existing filters
            if (array_key_exists($postdata['_sort_order'], $this->tableObj->orderList)) {
                if ('ASC' === $this->tableObj->orderList[$postdata['_sort_order']]) {
                    $this->tableObj->orderList[$postdata['_sort_order']] = 'DESC';
                } else {
                    unset($this->tableObj->orderList[$postdata['_sort_order']]);
                }
            } else {
                $this->tableObj->orderList[$postdata['_sort_order']] = 'ASC';
            }
        } elseif (false == $objectInSession) {
            // Initialization of default filters
            $query = "SELECT `cms_tbl_display_orderfields`.*
                    FROM `cms_tbl_display_orderfields`
                   WHERE `cms_tbl_display_orderfields`.`cms_tbl_conf_id` = '{$this->oTableConf->id}'
                ORDER BY `cms_tbl_display_orderfields`.`position` ASC
                 ";
            // desc because the sort list must be in reverse order
            $fieldList = MySqlLegacySupport::getInstance()->query($query);

            while ($field = MySqlLegacySupport::getInstance()->fetch_assoc($fieldList)) {
                $aTmpField = $this->TransformFieldForTranslations(['name' => $field['name'], 'db_alias' => '']);
                $this->tableObj->orderList[$aTmpField['name']] = $field['sort_order_direction'];
            }
        }

        // remove anything that is translated and not in current edit language #25819
        if (isset($this->tableObj->orderList) && is_array($this->tableObj->orderList) && count($this->tableObj->orderList) > 0) {
            /** @var BackendSessionInterface $backendSession */
            $backendSession = ServiceLocator::get('chameleon_system_cms_backend.backend_session');

            $editLanguage = $backendSession->getCurrentEditLanguageIso6391();
            foreach ($this->tableObj->orderList as $fullFieldName => $sortDirection) {
                $fieldNameParts = explode('.', $fullFieldName);
                $cleanFieldNamePart = trim(array_pop($fieldNameParts), '`');
                $matches = [];
                if (preg_match('@.*__([a-z][a-z])@', $cleanFieldNamePart, $matches)) {
                    $languageIdentifier = $matches[1];
                    if ($languageIdentifier !== $editLanguage) {
                        unset($this->tableObj->orderList[$fullFieldName]);
                    }
                }
            }
        }
    }

    /**
     * return the sort order as an order by sql string (without the "order by").
     *
     * @return string
     */
    public function GetSortInfoAsString()
    {
        $sSortOrder = '';
        $aOrderData = $this->tableObj->orderList;
        foreach ($aOrderData as $field => $direction) {
            if (!empty($sSortOrder)) {
                $sSortOrder .= ', ';
            }
            $sSortOrder .= $field.' '.$direction;
        }

        return $sSortOrder;
    }

    public function CallBackLockingStatus($field, $row, $name)
    {
        $userLock = TTools::IsRecordLocked($_SESSION['_tmpCurrentTableID'], $row['id']);
        if (false === $userLock) {
            return '<i class="fas fa-lock-open text-success" data-record-lock-status="unlocked"></i>';
        }

        $lockUser = $userLock->GetFieldCmsUser();

        return '<i class="fas fa-user-lock text-danger" data-record-lock-status="locked" title="'.TGlobal::OutHTML(ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_table_editor.header_lock')).': '.TGlobal::OutHTML($lockUser->GetName()).'"></i>';
    }

    /**
     * function displays the function icons (delete, copy, etc) for table lists
     * returns HTML.
     *
     * @param string $id
     * @param array $row
     *
     * @return string
     */
    public function CallBackFunctionBlock($id, $row)
    {
        $aFunctionItems = $this->getRowFunctionItems($id, $row);

        if (count($aFunctionItems) > 0) {
            $returnValue = '
        <div class="btn-group table-function-bar">
          <ul>
          ';

            foreach ($aFunctionItems as $key => $item) {
                if ('' === $item) {
                    continue;
                }
                $returnValue .= sprintf('<li class="table-function-%s" data-table-function="%s">%s</li>', $key, $key, $item);
            }
            $returnValue .= '
        </ul>
    </div>
        ';
        } else {
            $returnValue = '';
        }

        return $returnValue;
    }

    /**
     * Forms function item elements depending on user permissions.
     *
     * @param string $id
     * @param array $row
     *
     * @return array
     */
    protected function getRowFunctionItems($id, $row)
    {
        $items = [];

        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
        $fieldName = $this->oTableConf->sqlData['name'];

        if ($securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_EDIT, $fieldName)) {
            $items['edit'] = $this->CallBackFunctionBlockEditButton($id, $row);
        }

        if ($securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_NEW, $fieldName)) {
            $items['copy'] = $this->CallBackFunctionBlockCopyButton($id, $row);
        }

        if ($securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_DELETE, $fieldName)) {
            $items['delete'] = $this->CallBackFunctionBlockDeleteButton($id, $row);
        }

        return $items;
    }

    /**
     * Renders a button to open a record in full editing mode.
     *
     * @param string $id
     * @param array<string, string> $row
     *
     * @return string
     */
    public function CallBackFunctionBlockEditButton($id, $row)
    {
        $label = TGlobal::OutHTML(ServiceLocator::get('translator')->trans('chameleon_system_core.action.edit'));

        return '<span onclick="document.cmsform.id.value=\''.$row['id'].'\';document.cmsform.submit();" title="'.$label.'" class="fas fa-edit"></span>';
    }

    /**
     * Renders a button to duplicate records within the list view.
     *
     * @param string $id
     * @param array<string, string> $row
     *
     * @return string
     */
    public function CallBackFunctionBlockCopyButton($id, $row)
    {
        $label = TGlobal::OutHTML(ServiceLocator::get('translator')->trans('chameleon_system_core.action.copy'));

        if ((array_key_exists('cms_translation_parent_id', $row) && array_key_exists('cms_translationparentid', $row) && '' == $row['cms_translationparentid']) || !array_key_exists('cms_translation_parent_id', $row)) {
            return '<span onclick="document.cmsform.elements[\'module_fnc[contentmodule]\'].value=\'DatabaseCopy\';document.cmsform.id.value=\''.$row['id'].'\';document.cmsform.submit();" title="'.$label.'" class="fas fa-copy"></span>';
        }

        return '';
    }

    /**
     * Renders a button to delete records from the list view.
     *
     * @param string $id
     * @param array $row
     *
     * @return string
     */
    public function CallBackFunctionBlockDeleteButton($id, $row)
    {
        $label = TGlobal::OutHTML(ServiceLocator::get('translator')->trans('chameleon_system_core.action.delete'));

        return '<span onclick="DeleteRecord(\''.$row['id'].'\')" title="'.$label.'" class="fas fa-trash-alt text-danger"></span>';
    }

    /**
     * Returns a preview image with zoom on click.
     *
     * @param string $path
     * @param array $row
     *
     * @return string
     */
    public function CallBackImageWithZoom($path, $row)
    {
        $oImage = new TCMSImage();
        /* @var $oImage TCMSImage */
        $oImage->Load($row['id']);
        $image = '';

        if ($oImage->IsExternalMovie()) {
            $image = $oImage->renderImage(100, 75);
        } else {
            $oThumb = $oImage->GetThumbnail(80, 80);
            /** @var $oThumb TCMSImage */
            if (!is_null($oThumb)) {
                $oBigThumbnail = $oImage->GetThumbnail(400, 400);
                $imageZoomFnc = "CreateMediaZoomDialogFromImageURL('".$oBigThumbnail->GetFullURL()."','".TGlobal::OutHTML($oBigThumbnail->aData['width'])."','".TGlobal::OutHTML($oBigThumbnail->aData['height'])."');event.cancelBubble=true;return false;";
                $image = '<img src="'.$oThumb->GetFullURL()."\" id=\"cmsimage_{$row['id']}\" style=\"padding:3px\" width=\"{$oThumb->aData['width']}\" height=\"{$oThumb->aData['height']}\" border=\"0\" onclick=\"{$imageZoomFnc}\" />";
            }
        }

        return $image;
    }

    /**
     * returns a checkbox field for image file selection with javascript onlick.
     *
     * @param string $id
     * @param array $row
     *
     * @return string
     */
    public function CallBackMediaSelectBox($id, $row)
    {
        $html = "<input type=\"checkbox\" name=\"functionSelection[]\" value=\"{$id}\" onclick=\"parent.ChangeFileSelection('{$id}')\" />";

        return $html;
    }

    /**
     * returns the document filename croped to 25 chars max.
     *
     * @param string $filename
     * @param array $row
     *
     * @return string
     */
    public function CallBackFilenameShort($filename, $row)
    {
        $shortFilename = $filename;
        if (mb_strlen($shortFilename) > 25) {
            $shortFilename = mb_substr($shortFilename, 0, 25).'...';
        }

        return $shortFilename;
    }

    /**
     * returns the document filesize.
     *
     * @param string $fileSize
     * @param array $row
     *
     * @return string
     */
    public function CallBackHumanRedableFileSize($fileSize, $row)
    {
        $fileSize = TCMSDownloadFile::GetHumanReadableFileSize($fileSize);

        return $fileSize;
    }

    /**
     * returns checkbox field for multiple file selections.
     *
     * @param string $id
     * @param array $row
     * @param string $sFieldName
     *
     * @return string
     */
    public function CallBackDrawListItemSelectbox($id, $row, $sFieldName)
    {
        $html = '';
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
        if ($securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_DELETE, $this->oTableConf->sqlData['name'])) {
            $html = '<input type="checkbox" name="aInputIdList[]" value="'.TGlobal::OutHTML($id).'" />';
        }

        return $html;
    }

    /**
     * returns a CSS class that styles the row.
     *
     * @param string $sRecordID
     * @param array $row
     *
     * @return string
     */
    public function CallBackRowStyling($sRecordID, $row)
    {
        return '';
    }

    /**
     * @return Connection
     */
    private function getDatabaseConnection()
    {
        return ServiceLocator::get('database_connection');
    }

    /**
     * @return ClassFromTableFieldProviderInterface
     */
    private function getClassFromTableFieldProvider()
    {
        return ServiceLocator::get('chameleon_system_core.field.provider.class_from_table_field');
    }

    private function getInputFilterUtil(): InputFilterUtilInterface
    {
        return ServiceLocator::get('chameleon_system_core.util.input_filter');
    }

    private function getToolsService(): TTools
    {
        return ServiceLocator::get('chameleon_system_core.tools');
    }
}
