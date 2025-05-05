<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\FieldTranslationUtil;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\CoreBundle\Util\MltFieldUtil;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsPermissionAttributeConstants;
use ChameleonSystem\SecurityBundle\Voter\CmsUserRoleConstants;
use Doctrine\DBAL\Connection;

/**
 * class used to display a list of a table. all list views must inherit from
 * this class.
 */
class TCMSListManagerEndPoint
{
    public $sRestriction;
    public $sRestrictionField;
    public $fieldCount; // nr. of appearance of this field

    /**
     * table definition object.
     *
     * @var TdbCmsTblConf
     */
    protected $oTableConf;

    /**
     * an iterator of the menu items for the table (new, export, etc).
     *
     * @var TIterator
     */
    protected $oMenuItems;

    /**
     * set this to false if you want to prevent table list caching (session).
     *
     * @var bool
     */
    protected $bListCacheEnabled = true;

    /**
     * array of methods that are allowed to be called via URL (ajax call)
     * /cms?module_fnc[contentmodule]=ExecuteAjaxCall&_noModuleFunction=true&_fnc=FunctionToCall&pagedef=tablemanager&id=7ab61897-3cf5-de32-a8f1-b926c650d756&callListManagerMethod=1.
     *
     * @var array
     */
    protected $methodCallAllowed = [];

    /**
     * init the list class.
     *
     * @param TdbCmsTblConf $oTableConf
     */
    public function Init($oTableConf)
    {
        $this->oTableConf = $oTableConf;
    }

    /**
     * sets methods that are allowed to be called via URL (ajax calls).
     */
    protected function DefineInterface()
    {
    }

    /**
     * checks if method is listed in $this->methodCallAllowed array.
     *
     * @param string $sMethodName
     *
     * @return bool
     */
    public function IsMethodCallAllowed($sMethodName)
    {
        $this->DefineInterface();
        $returnVal = false;
        if (in_array($sMethodName, $this->methodCallAllowed)) {
            $returnVal = true;
        }

        return $returnVal;
    }

    /**
     * returns the table as a string. it would be better to return an object,
     * but the old way of handling the tables does not allow this.
     *
     * @return string
     */
    public function GetList()
    {
        return 'table';
    }

    /**
     * checks if the user has the right to see the table list.
     *
     * @return bool
     */
    public function CheckTableRights()
    {
        $tableRights = false;
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
        $tableInUserGroup = $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_ACCESS, $this->oTableConf->fieldName);
        if ($tableInUserGroup) {
            $tableRights = true;
        }

        return $tableRights;
    }

    /**
     * generate the query for the current view.
     *
     * @return string
     */
    public function FilterQuery()
    {
        // the table query comes either from the field designated for it,
        // or we need to generate the query by hand.
        $filterQuery = '';

        if (is_array($this->oTableConf->sqlData) && array_key_exists('list_query', $this->oTableConf->sqlData)) {
            $recordquery = trim($this->oTableConf->sqlData['list_query']);
            $portalRestriction = $this->GetPortalRestriction();
            $userRestriction = $this->GetUserRestriction();
            $userRestrictionJoin = '';
            if (!empty($userRestriction) && false !== strpos($userRestriction, '_cms_usergroup_mlt')) {
                $userRestrictionJoin = $this->GetUserRestrictionJoin($recordquery);
            }
            if (!empty($recordquery)) {
                $filterQuery = str_replace("\n", ' ', $recordquery);
                $filterQuery = str_replace('  ', ' ', $filterQuery);
                $databaseConnection = $this->getDatabaseConnection();
                $quotedTableName = $databaseConnection->quoteIdentifier($this->oTableConf->sqlData['name']);
                $filterQuery = preg_replace("/(FROM\s.+?(\s|$))/", "FROM $quotedTableName ", $filterQuery);
                if (false !== strpos($filterQuery, 'WHERE')) {
                    $filterQuery = str_replace('WHERE', $this->GetFilterQueryCustomJoins()." $userRestrictionJoin WHERE ", $filterQuery);
                } else {
                    $filterQuery = str_ireplace("FROM $quotedTableName ", "FROM $quotedTableName ".$this->GetFilterQueryCustomJoins()." $userRestrictionJoin", $filterQuery);
                }

                // if we don't have a where condition we will need to add one
                if (false === mb_strpos($filterQuery, 'WHERE')) {
                    $filterQuery .= ' WHERE 1=1';
                }
            } else {
                $filterQuery = 'SELECT * FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($this->oTableConf->sqlData['name']).'` '.$this->GetFilterQueryCustomJoins().' '.$userRestrictionJoin.' WHERE 1=1';
            }

            if (!empty($portalRestriction)) {
                if (stristr($filterQuery, 'GROUP BY')) {
                    $filterQuery = str_replace('WHERE', 'WHERE ('.$portalRestriction.') AND ', $filterQuery);
                } else {
                    $filterQuery .= ' AND ('.$portalRestriction.')';
                }
            }

            if (!empty($userRestriction)) {
                if (stristr($filterQuery, 'GROUP BY')) {
                    $filterQuery = str_replace('WHERE', 'WHERE ('.$userRestriction.') AND ', $filterQuery);
                } else {
                    $filterQuery .= ' AND ('.$userRestriction.')';
                }
            }

            $customRestriction = $this->GetCustomRestriction();
            if (!empty($customRestriction)) {
                if (stristr($filterQuery, 'GROUP BY')) {
                    $filterQuery = str_replace('WHERE', 'WHERE ('.$customRestriction.') AND ', $filterQuery);
                } else {
                    $filterQuery .= ' AND ('.$customRestriction.')';
                }
            }

            // now add custom restrictions if present
            $oCustomRestrictions = $this->oTableConf->GetProperties('cms_tbl_conf_restrictions', 'TCMSTableConfRestriction');
            while ($oCustomRestriction = $oCustomRestrictions->Next()) {
                /** @var $oCustomRestriction TCMSTableConfRestriction */
                $sCustomRestriction = $oCustomRestriction->GetRestriction($this->oTableConf);
                if (!empty($sCustomRestriction)) {
                    $filterQuery .= ' AND ('.$sCustomRestriction.')';
                }
            }

            $sCustomGroupBy = $this->GetCustomGroupBy();
            $filterQuery .= $sCustomGroupBy;
        }

        return $filterQuery;
    }

    /**
     * Add Group By to the end of the query.
     *
     * @return string
     */
    protected function GetCustomGroupBy()
    {
        $oRecordList = call_user_func([TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $this->oTableConf->sqlData['name']).'List', 'GetList']);
        $sQuery = $oRecordList->GetListManagerCustomGroupBy($this);

        return $sQuery;
    }

    /**
     * Add custom joins to the query.
     *
     * @return string
     */
    protected function GetFilterQueryCustomJoins()
    {
        $oRecordList = call_user_func([TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $this->oTableConf->sqlData['name']).'List', 'GetList']);
        $sQuery = $oRecordList->GetListManagerFilterQueryCustomJoins($this);

        return $sQuery;
    }

    /**
     * by returning false the "new entry" button in the list can be supressed.
     *
     * @return bool
     */
    public function ShowNewEntryButton()
    {
        return true;
    }

    protected function GetPortalRestrictionJoin()
    {
        $query = '';

        // we add the portal restriction ONLY if we are not in the cms_admin group (admins may see all portals)
        $portalMLTFieldExists = TTools::FieldExists($this->oTableConf->sqlData['name'], 'cms_portal_mlt', true);
        $mltTable = $this->oTableConf->sqlData['name'].'_cms_portal_mlt';
        $databaseConnection = $this->getDatabaseConnection();
        $quotedMltTable = $databaseConnection->quoteIdentifier($mltTable);

        $bPortalJoinExists = false;
        $sRecordQuery = trim($this->oTableConf->sqlData['list_query']);
        if (false !== strpos($sRecordQuery, "JOIN $quotedMltTable")) {
            $bPortalJoinExists = true;
        }

        if ($portalMLTFieldExists && !$bPortalJoinExists) {
            $query .= " LEFT JOIN $quotedMltTable ON `".MySqlLegacySupport::getInstance()->real_escape_string($this->oTableConf->sqlData['name'])."`.`id` = $quotedMltTable.`source_id`";
        }

        return $query;
    }

    /**
     * changes the query so that the portal restriction is added.
     *
     * @return string
     */
    public function GetPortalRestriction()
    {
        $query = '';
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
        if (false === $securityHelper->isGranted(CmsUserRoleConstants::CMS_USER)) {
            return false;
        }

        $portals = $securityHelper->getUser()?->getPortals();
        if (null === $portals) {
            $portals = [];
        }
        $portalRestrictions = implode(', ', array_map(fn ($id) => $this->getDatabaseConnection()->quote($id), array_keys($portals)));

        // we add the portal restriction ONLY if the user does not have the cms_admin role (admins may see all portals)
        if (false === $securityHelper->isGranted(CmsUserRoleConstants::CMS_ADMIN)) {
            $databaseConnection = $this->getDatabaseConnection();
            $sTableName = $this->oTableConf->sqlData['name'];
            $quotedTableName = $databaseConnection->quoteIdentifier($sTableName);
            $portalMLTFieldExists = TTools::FieldExists($this->oTableConf->sqlData['name'], 'cms_portal_mlt', true);
            $portalIDFieldExists = TTools::FieldExists($this->oTableConf->sqlData['name'], 'cms_portal_id');

            $portalLinkExists = ($portalMLTFieldExists || $portalIDFieldExists);
            if ($portalLinkExists) {
                $restriction = '';
                $mltTable = $this->oTableConf->sqlData['name'].'_cms_portal_mlt';
                $quotedMltTable = $databaseConnection->quoteIdentifier($mltTable);
                if ('' !== $portalRestrictions) { // the user is in portals...
                    if ($portalMLTFieldExists) {
                        // mlt connection (record may be in many portals)

                        $mltSubSelect = $this->getSubselectForMlt($quotedTableName, $quotedMltTable);
                        $mltSubSelect .= " OR $quotedMltTable.`target_id` IN ($portalRestrictions)";

                        $restriction .= " $quotedTableName.`id` IN ($mltSubSelect)";
                    }
                    if ($portalIDFieldExists) {
                        if (!empty($restriction)) {
                            $restriction .= ' AND ';
                        }
                        $restriction .= " ($quotedTableName.`cms_portal_id` IN ($portalRestrictions) OR $quotedTableName.`cms_portal_id` = '' OR $quotedTableName.`cms_portal_id` = '0')";
                    }
                } else {
                    if ($portalMLTFieldExists) {
                        // mlt connection (record may be in many portals)

                        $mltSubSelect = $this->getSubselectForMlt($quotedTableName, $quotedMltTable);

                        $restriction .= " $quotedTableName.`id` IN ($mltSubSelect)";
                    }
                    if ($portalIDFieldExists) {
                        if (!empty($restriction)) {
                            $restriction .= ' AND ';
                        }
                        $restriction .= " ($quotedTableName.`cms_portal_id` = 0)";
                    }
                }
                $query .= " $restriction";
            } elseif ('`cms_portal`.`name`' === $this->oTableConf->sqlData['list_group_field'] || 'cms_portal' === $this->oTableConf->sqlData['name']) {
                // if the portal is the group field, or we are the portal table then we will need to restrict by portal as well...
                if ('' === $portalRestrictions) {
                    $query = ' 1=2';
                } else {
                    $query = " (`cms_portal`.`id` IN ($portalRestrictions))";
                }
            }
        }

        $oRecordList = call_user_func([TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $this->oTableConf->sqlData['name']).'List', 'GetList']);
        $query = $oRecordList->GetListManagerPortalRestriction($this, $query);

        return $query;
    }

    private function getSubselectForMlt(string $quotedTableName, string $quotedMltTableName): string
    {
        $mltSubSelect = "SELECT DISTINCT $quotedTableName.`id` FROM $quotedTableName";
        $mltSubSelect .= $this->GetPortalRestrictionJoin();
        $mltSubSelect .= " WHERE $quotedMltTableName.`target_id` IS NULL";

        return $mltSubSelect;
    }

    public function CreateRestriction($fieldname, $operatorAndValue)
    {
        return "`{$this->oTableConf->sqlData['name']}`.`".MySqlLegacySupport::getInstance()->real_escape_string($fieldname).'` '.$operatorAndValue;
    }

    /**
     * show only records that belong to the user (if the table contains the user id).
     *
     * @return string
     */
    public function GetUserRestriction()
    {
        $query = '';
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
        $userId = $securityHelper->getUser()?->getId();
        $userGroupIds = $securityHelper->getUser()?->getGroups();
        if (null === $userGroupIds) {
            $userGroupIds = [];
        }
        if (0 === count($userGroupIds)) {
            $groupList = false;
        } else {
            $groupList = implode(', ', array_map(fn ($id) => $this->getDatabaseConnection()->quote($id), array_keys($userGroupIds)));
        }

        $restrictionQuery = '';
        if (!$securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_ACCESS, $this->oTableConf->sqlData['name'])) {
            // user does not have the right to view or edit records he did not create. does the table
            // contain a user field (cms_user_id)
            // a user that does not have permission to see all records may still view a record if it
            // is his own, or if he is in the user group list
            $tmpQuery = "SHOW FIELDS FROM `{$this->oTableConf->sqlData['name']}` LIKE 'cms_user_id'";
            $userField = MySqlLegacySupport::getInstance()->query($tmpQuery);

            if (MySqlLegacySupport::getInstance()->num_rows($userField) > 0) {
                $restrictionQuery = $this->CreateRestriction('cms_user_id', "= '".$userId."'");
            }

            // check for user groups
            $tmpQuery = "SELECT * FROM `cms_field_conf` WHERE `name` LIKE 'cms_usergroup_mlt' AND `cms_tbl_conf_id` = '".$this->oTableConf->id."' ";
            // $tmpQuery = "SHOW FIELDS FROM `{$this->oTableConf->sqlData['name']}` LIKE 'cms_usergroup_mlt'";
            $userField = MySqlLegacySupport::getInstance()->query($tmpQuery);
            if (MySqlLegacySupport::getInstance()->num_rows($userField) > 0) {
                if (false !== $groupList) {
                    if (!empty($restrictionQuery)) {
                        $restrictionQuery .= ' OR ';
                    }
                    $restrictionQuery .= " `{$this->oTableConf->sqlData['name']}_cms_usergroup_mlt`.`target_id` IN ({$groupList})";
                }
            }

            if (!empty($restrictionQuery)) {
                $query .= " {$restrictionQuery}";
            }
        }

        return $query;
    }

    protected function GetUserRestrictionJoin($recordQuery)
    {
        $mltTableName = $this->oTableConf->sqlData['name'].'_cms_usergroup_mlt';
        if (1 != preg_match("#(JOIN\s`?".$mltTableName."(`|\s))#", $recordQuery)) {
            $databaseConnection = $this->getDatabaseConnection();
            $quotedMltTableName = $databaseConnection->quoteIdentifier($mltTableName);
            $quotedTableName = $databaseConnection->quoteIdentifier($this->oTableConf->sqlData['name']);

            return " LEFT JOIN $quotedMltTableName ON $quotedTableName.`id` = $quotedMltTableName.`source_id`";
        }

        return '';
    }

    /**
     * any custom restrictions can be added to the query by overwriting this function.
     *
     * @return string
     *
     * @throws Doctrine\DBAL\Exception
     */
    public function GetCustomRestriction()
    {
        if (true === empty($this->sRestrictionField) || null === $this->sRestriction) {
            return '';
        }

        $sourceID = $this->sRestriction;
        $fieldName = $this->sRestrictionField;
        $foreignTableName = $this->oTableConf->sqlData['name'];
        $connection = $this->getDatabaseConnection();

        if (false === str_ends_with($fieldName, '_mlt') && true === $this->getToolsService()::FieldExists($foreignTableName, $fieldName)) {
            return $this->CreateRestriction($this->sRestrictionField, '= '.$connection->quote($sourceID));
        }

        $mltTable = $this->GetMLTTableName();
        if (false === TGlobalBase::TableExists($mltTable)) {
            return '1=0'; // mlt table does not exist, so the restriction is invalid
        }

        $query = sprintf('SELECT target_id FROM %s WHERE source_id = :value', $connection->quoteIdentifier($mltTable));

        $idList = $connection->fetchFirstColumn($query, ['value' => $this->sRestriction]);
        if ([] === $idList) {
            return '1=0'; // restrictions set, but no matches
        }

        $idListString = implode(',', array_map([$connection, 'quote'], $idList));
        $quotedTableName = $connection->quoteIdentifier($foreignTableName);

        return " $quotedTableName.id IN ($idListString)";
    }

    protected function GetMLTTableName()
    {
        return substr($this->sRestrictionField, 0, -4).'_'.$this->GetFieldMltName().'_mlt';
    }

    /**
     * Returns the name of the MLt field without source table name.
     * Postfix _mlt was filtered.
     *
     * @return string
     */
    protected function GetFieldMltName()
    {
        $fieldMltName = $this->oTableConf->sqlData['name'];
        $postFieldMltName = $this->tableObj->_postData['name'] ?? null;
        if (null === $postFieldMltName) {
            return $fieldMltName;
        }

        $mltFieldUtil = $this->getMltFieldUtil();
        $postFieldMltName = $mltFieldUtil->cutMltExtension($postFieldMltName);
        $cleanMltFieldName = $mltFieldUtil->cutMultiMltFieldNumber($postFieldMltName);
        if ($cleanMltFieldName === $fieldMltName) {
            return $postFieldMltName;
        }

        return $postFieldMltName.'_'.$fieldMltName;
    }

    public function GetTableAlias($query)
    {
        $databaseConnection = $this->getDatabaseConnection();
        $parentTableAlias = $databaseConnection->quoteIdentifier($this->oTableConf->sqlData['name']);

        $query = str_replace("\n", ' ', $query);

        // get parent table alias
        if (stristr($query, "FROM $parentTableAlias AS ")) { // we have an alias
            $aQuery = explode("FROM $parentTableAlias AS ", $query);
            $aQuery2 = explode(' ', $aQuery[1]);
            $parentTableAlias = trim($aQuery2[0]);
        }

        return $parentTableAlias;
    }

    /**
     * returns an iterator with the menuitems for the current table. if you want to add your own
     * items, overwrite the GetCustomMenuItems (NOT GetMenuItems)
     * the iterator will always be reset to start.
     *
     * @return TIterator
     */
    public function GetMenuItems()
    {
        if (is_null($this->oMenuItems)) {
            $this->oMenuItems = new TIterator();
            // std menuitems...

            /** @var SecurityHelperAccess $securityHelper */
            $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
            $allowTableAccess = $securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_ACCESS, $this->oTableConf->fieldName);
            if ($allowTableAccess) {
                if ($securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_NEW, $this->oTableConf->fieldName)) {
                    // new
                    $oMenuItem = new TCMSTableEditorMenuItem();
                    $oMenuItem->sDisplayName = ServiceLocator::get('translator')->trans('chameleon_system_core.action.new');
                    $oMenuItem->sIcon = 'fas fa-plus';
                    $oMenuItem->sItemKey = 'new';
                    $oMenuItem->sOnClick = "document.cmsform.elements['module_fnc[contentmodule]'].value='Insert';document.cmsform.submit();";
                    $this->oMenuItems->AddItem($oMenuItem);
                }

                // if we have edit access to the table editor, then we also show a link to it
                if ($securityHelper->isGranted(CmsPermissionAttributeConstants::TABLE_EDITOR_EDIT, 'cms_tbl_conf')) {
                    $oTableEditorConf = new TCMSTableConf();
                    /* @var $oTableEditorConf TCMSTableConf */
                    $oTableEditorConf->LoadFromField('name', 'cms_tbl_conf');
                    $oMenuItem = new TCMSTableEditorMenuItem();
                    $oMenuItem->sItemKey = 'edittableconf';
                    $oMenuItem->sDisplayName = ServiceLocator::get('translator')->trans('chameleon_system_core.action.open_table_configuration');
                    $oMenuItem->sIcon = 'fas fa-cogs';
                    $oMenuItem->setButtonStyle('btn-warning');

                    $pagedef = 'tableeditor';
                    if (true === $this->isLoadedInIframe()) {
                        $pagedef = 'tableeditorPopup';
                    }

                    $aParameter = ['pagedef' => $pagedef, 'id' => $this->oTableConf->id, 'tableid' => $oTableEditorConf->id];
                    $aAdditionalParams = $this->GetHiddenFieldsHook();
                    if (is_array($aAdditionalParams) && count($aAdditionalParams) > 0) {
                        $aParameter = array_merge($aParameter, $aAdditionalParams);
                    }

                    $oMenuItem->href = PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURLForJavascript($aParameter); // TODO support "top" target?
                    $this->oMenuItems->AddItem($oMenuItem);
                }

                // now add custom items
                $this->GetCustomMenuItems();
            }
        } else {
            $this->oMenuItems->GoToStart();
        }

        return $this->oMenuItems;
    }

    private function isLoadedInIframe(): bool
    {
        return '1' === $this->getInputFilterUtil()->getFilteredGetInput('_isiniframe');
    }

    /**
     * add table-specific buttons to the editor (add them directly to $this->oMenuItems).
     */
    protected function GetCustomMenuItems()
    {
    }

    /**
     * return an array of all js, css, or other header includes that are required
     * in the cms for this field. each include should be in one line, and they
     * should always be typed the same way so that no includes are included mor than once.
     *
     * @return array
     */
    public function GetHtmlHeadIncludes()
    {
        $aIncludes = [];

        return $aIncludes;
    }

    /**
     * just a stub to change the created table object function of any children.
     */
    protected function PostCreateTableObjectHook()
    {
    }

    /**
     * return array of hidden fields (key=>value) that will be added to
     * MTTableManager forms.
     *
     * @return array
     */
    public function GetHiddenFieldsHook()
    {
        $oGlobal = TGlobal::instance();
        $aAdditionalParameterData = [];
        $aAdditionalParameters = ['sRestrictionField', 'sRestriction', 'bIsLoadedFromIFrame'];
        foreach ($aAdditionalParameters as $sKey) {
            if ($oGlobal->UserDataExists($sKey)) {
                $aAdditionalParameterData[$sKey] = $oGlobal->GetUserData($sKey);
            }
        }

        return $aAdditionalParameterData;
    }

    /**
     * @return Connection
     */
    private function getDatabaseConnection()
    {
        return ServiceLocator::get('database_connection');
    }

    /**
     * @return FieldTranslationUtil
     */
    protected function getFieldTranslationUtil()
    {
        return ServiceLocator::get('chameleon_system_core.util.field_translation');
    }

    private function getInputFilterUtil(): InputFilterUtilInterface
    {
        return ServiceLocator::get('chameleon_system_core.util.input_filter');
    }

    /**
     * @return MltFieldUtil
     */
    protected function getMltFieldUtil()
    {
        return ServiceLocator::get('chameleon_system_core.util.mlt_field');
    }

    private function getToolsService(): TTools
    {
        return ServiceLocator::get('chameleon_system_core.tools');
    }
}
