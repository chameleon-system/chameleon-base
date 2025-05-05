<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\ActivePageServiceInterface;
use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;

/**
 * a list which can be completely customized. it assumes that we have a category table (optional)
 * a list table, items that define each entry (defaults to TCMSRecord).
 * /**/
class MTCustomListCore extends TUserCustomModelBase
{
    /**
     * list config object.
     *
     * @var MTCustomListCoreConfig
     */
    protected $oListConfig;

    /**
     * the list config table.
     *
     * @var TdbModuleCustomlistConfig
     */
    protected $oModuleConfig;

    /**
     * the TCMSTableConfig of the list Table.
     *
     * @var TCMSTableConf
     */
    protected $oListTableConfig;

    /**
     * the current loaded item.
     *
     * @var object
     */
    protected $oActiveItem;

    /**
     * recordList object of all items.
     *
     * @var TCMSRecordList
     */
    protected $oItemList;

    /**
     * current page count.
     *
     * @var int
     */
    protected $iPage = 0;

    /**
     * URL parameter to set current page.
     *
     * @var string
     */
    protected $ipageURLParam = 'ipage';

    protected $bAllowHTMLDivWrapping = true;

    public function Init()
    {
        parent::Init();
        if ($this->global->UserDataExists($this->ipageURLParam)) {
            $this->iPage = $this->global->GetUserData($this->ipageURLParam);
        } else {
            $this->iPage = 0;
        }
        $this->LoadListConfig();
    }

    public function Execute()
    {
        parent::Execute();

        $this->LoadModuleConfigTable();

        $this->data['oItemListConfig'] = $this->oModuleConfig;
        $this->data['oItem'] = $this->oActiveItem;

        if (is_null($this->oActiveItem)) { // no item -> show list
            $this->oItemList = $this->GetList();
            $this->GetListNavigation();
            $this->data['oItemList'] = $this->oItemList;
        }

        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    protected function DefineInterface()
    {
        parent::DefineInterface();
        $externalFunctions = ['ShowItem'];
        $this->methodCallAllowed = array_merge($this->methodCallAllowed, $externalFunctions);
    }

    public function GetListNavigation()
    {
        $sNextPageLink = '';
        $sPageURL = $this->getActivePageService()->getLinkToActivePageRelative();
        if ($this->oModuleConfig->sqlData['records_per_page'] > 0 && (($this->iPage + 1) * $this->oModuleConfig->sqlData['records_per_page'] <= $this->oItemList->Length() - 1)) {
            $sNextPageLink = $sPageURL.'?'.$this->ipageURLParam.'='.($this->iPage + 1);
        }
        $sPreviousPageLink = '';
        if ($this->iPage > 0) {
            $sPreviousPageLink = $sPageURL.'?'.$this->ipageURLParam.'='.($this->iPage - 1);
        }

        $this->data['sNextPageLink'] = $sNextPageLink;
        $this->data['sPreviousPageLink'] = $sPreviousPageLink;

        // total pages
        if ($this->oModuleConfig->sqlData['records_per_page'] >= 1) {
            $this->data['numberOfPages'] = ceil($this->oItemList->Length() / $this->oModuleConfig->sqlData['records_per_page']);
        } else {
            $this->data['numberOfPages'] = 1;
        }

        $this->data['iPage'] = $this->iPage + 1;
    }

    /**
     * Show one item of the list...
     */
    public function ShowItem()
    {
        $itemId = $this->global->GetUserData('itemid');
        $sClassName = $this->oListConfig->sItemClass;
        $this->oActiveItem = new $sClassName();
        $this->oActiveItem->table = $this->oListConfig->sListTable;

        $this->oActiveItem->SetLanguage($this->getLanguageService()->getActiveLanguageId());
        $this->oActiveItem->Load($itemId);
        $this->SetTemplate($this->GetModelName(), 'inc/item');
    }

    protected function GetModelName()
    {
        return get_class($this);
    }

    protected function GetList()
    {
        $oListRecords = new TCMSRecordList(); /* var $oListRecords TCMSRecordList */

        $oListRecords->sTableName = $this->oListConfig->sListTable;
        $oListRecords->SetLanguage($this->getLanguageService()->getActiveLanguageId());
        $oListRecords->sTableObject = $this->oListConfig->sItemClass;

        $pageSize = $this->oModuleConfig->sqlData['records_per_page'];
        if (0 == $pageSize) {
            $pageSize = -1;
        }
        $startRecord = $this->iPage * $pageSize;

        $oListRecords->Load($this->GetListQuery());
        $oListRecords->SetPagingInfo($startRecord, $pageSize);

        return $oListRecords;
    }

    /**
     * Extend this method to config the oListConfig object.
     */
    protected function LoadListConfig()
    {
        $this->oListConfig = new MTCustomListCoreConfig();
        $this->oListTableConfig = new TCMSTableConf();
        $this->oListTableConfig->LoadFromField('name', $this->oListConfig->sListTable);
    }

    /**
     * loads the list config table.
     */
    protected function LoadModuleConfigTable()
    {
        /**
         * The table is dynamic, but most of the times it will be TdbModuleCustomlistConfig.
         *
         * @var TdbModuleCustomlistConfig $moduleConfiguration
         */
        $moduleConfigurationTableName = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $this->oListConfig->sListConfigTable);
        $moduleConfiguration = call_user_func([$moduleConfigurationTableName, 'GetNewInstance']);
        $moduleConfiguration->SetLanguage($this->getLanguageService()->getActiveLanguageId());
        $moduleConfiguration->LoadFromField('cms_tpl_module_instance_id', $this->instanceID);
        $this->oModuleConfig = $moduleConfiguration;
    }

    protected function GetListQuery()
    {
        $sEscapedListTableName = MySqlLegacySupport::getInstance()->real_escape_string($this->oListConfig->sListTable);
        $query = "SELECT `{$sEscapedListTableName}`.*";
        // join in the category if present
        $sCategoryTableName = null;
        $sCategoryNameField = null;
        if (!is_null($this->oListConfig->sCategoryFieldName)) {
            $oCategoryField = $this->oListTableConfig->GetFieldDefinition($this->oListConfig->sCategoryFieldName);

            // mlt, lookup, or normal?
            $oCategoryFieldType = $oCategoryField->GetFieldType();
            switch ($oCategoryFieldType->sqlData['constname']) {
                case 'CMSFIELD_TABLELIST':
                case 'CMSFIELD_COUNTRY':
                case 'CMSFIELD_TREE':
                case 'CMSFIELD_EXTENDEDTABLELIST':
                    $sCategoryTableName = MySqlLegacySupport::getInstance()->real_escape_string(substr($this->oListConfig->sCategoryFieldName, 0, -3)); // cut out _id
                    $sCategoryNameField = $this->GetCategoryNameField($sCategoryTableName);
                    $query .= ", `{$sCategoryTableName}`.`id` AS _category_id, `{$sCategoryTableName}`.`".MySqlLegacySupport::getInstance()->real_escape_string($sCategoryNameField).'` AS _category_name ';
                    $query .= " FROM `{$sEscapedListTableName}`";
                    $query .= "LEFT JOIN `{$sCategoryTableName}`  ON `{$sEscapedListTableName}`.`".MySqlLegacySupport::getInstance()->real_escape_string($this->oListConfig->sCategoryFieldName)."` = `{$sCategoryTableName}`.`id` ";
                    break;
                case 'CMSFIELD_MULTITABLELIST':
                case 'CMSFIELD_MULTITABLELIST_CHECKBOXES':
                    $sCategoryTableName = MySqlLegacySupport::getInstance()->real_escape_string(substr($this->oListConfig->sCategoryFieldName, 0, -4)); // cut out _mlt
                    $sCategoryNameField = $this->GetCategoryNameField($sCategoryTableName);
                    $mltTable = MySqlLegacySupport::getInstance()->real_escape_string($this->oListConfig->sListTable.'_'.$this->oListConfig->sCategoryFieldName);
                    $query .= ", `{$sCategoryTableName}`.`id` AS _category_id, `{$sCategoryTableName}`.`".MySqlLegacySupport::getInstance()->real_escape_string($sCategoryNameField).'` AS _category_name ';
                    $query .= " FROM `{$sEscapedListTableName}`";
                    $query .= "LEFT JOIN `{$mltTable}` ON `{$sEscapedListTableName}`.`id` = `{$mltTable}`.`source_id`
        	             LEFT JOIN `{$sCategoryTableName}` ON `{$mltTable}`.`target_id` = `{$sCategoryTableName}`.`id`
        	  ";
                    break;
                default:
                    $query .= " FROM `{$sEscapedListTableName}` ";
                    break;
            }
        } else {
            $query .= " FROM `{$sEscapedListTableName}` ";
        }
        // add instance id
        $query .= "WHERE `{$sEscapedListTableName}`.`cms_tpl_module_instance_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->instanceID)."' ";
        $query .= $this->GetListOrder($sCategoryTableName, $sCategoryNameField);

        return $query;
    }

    /**
     * @param string|null $sCategoryTable
     * @param string|null $sCategoryNameField
     *
     * @return string
     */
    public function GetListOrder($sCategoryTable = null, $sCategoryNameField = null)
    {
        $query = '';
        if (!is_null($sCategoryTable)) {
            $query .= " `{$sCategoryTable}`.`".MySqlLegacySupport::getInstance()->real_escape_string($sCategoryNameField).'`';
        }

        // now either add order info from config list, or just use the name field of the list
        $oOrderFields = $this->oModuleConfig->GetFieldOrderinfoList();
        $oOrderFields->ChangeOrderBy(['position' => 'ASC']);

        if ($oOrderFields->Length() > 0) {
            while ($oOrderField = $oOrderFields->Next()) {
                if (!empty($query)) {
                    $query .= ',';
                }
                $query .= ' '.MySqlLegacySupport::getInstance()->real_escape_string($oOrderField->sqlData['name']).' '.$oOrderField->sqlData['direction'];
            }
        } else {
            if (!empty($query)) {
                $query .= ',';
            }
            $sListNameField = $this->oListTableConfig->GetNameColumn();
            $query .= ' `'.MySqlLegacySupport::getInstance()->real_escape_string($this->oListConfig->sListTable).'`.`'.MySqlLegacySupport::getInstance()->real_escape_string($sListNameField).'`';
        }

        if (!empty($query)) {
            $query = 'ORDER BY '.$query;
        }

        return $query;
    }

    protected function GetCategoryNameField($sCategoryTableName)
    {
        $oCatConf = new TCMSTableConf();
        /* @var $oCatConf TCMSTableConf */
        $oCatConf->LoadFromField('name', $sCategoryTableName);

        return $oCatConf->GetNameColumn();
    }

    /**
     * if this function returns true, then the result of the execute function will be cached.
     *
     * @return bool
     */
    public function _AllowCache()
    {
        return false;
    }

    /**
     * @return ActivePageServiceInterface
     */
    private function getActivePageService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.active_page_service');
    }

    /**
     * @return LanguageServiceInterface
     */
    private function getLanguageService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.language_service');
    }
}
