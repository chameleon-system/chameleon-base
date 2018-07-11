<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @deprecated since 6.2.0 - no longer used.
 */
class MTListCore extends TUserCustomModelBase
{
    /**
     * holds basic config data about the list.
     *
     * @var TdbModuleListConfig
     */
    protected $_oListSettings = null;

    /**
     * the data relating to the instance of the list.
     *
     * @var TdbCmsTplModuleInstance
     */
    protected $_oModuleInstanceData = null;

    /**
     * module_list record object.
     *
     * @var TdbModuleListList
     */
    protected $oModuleListList = null;

    protected $bAllowHTMLDivWrapping = true;

    /**
     * the list object of categories.
     *
     * @var TdbModuleListCatList
     */
    protected $oModuleListCatList = null;

    public function &Execute()
    {
        $this->data = parent::Execute();

        $this->LoadInstanceInfo();
        $id = $this->global->GetUserData('article'.$this->instanceID);
        if ($id) {
            $this->GetDetailsData();
        } else {
            $this->data['oList'] = $this->GetListObject();
            $this->data['oModuleListCatList'] = $this->oModuleListCatList;
        }

        return $this->data;
    }

    protected function GetDetailsData()
    {
        $id = $this->global->GetUserData('article'.$this->instanceID);
        $oArticle = TdbModuleList::GetNewInstance();
        $oArticle->Load($id);
        $this->data['oArticle'] = $oArticle;
        $this->SetTemplate('MTList', 'inc/detail');
    }

    protected function GetListObject()
    {
        if (is_null($this->oModuleListList)) {
            $secondarySort = '`module_list`.`position` ASC, `module_list`.`date_today` DESC, `module_list`.`name` ASC';

            if ($this->_oListSettings && is_array($this->_oListSettings->sqlData) && !empty($this->_oListSettings->sqlData['module_list_cmsfieldname'])) {
                $secondarySort = '`module_list`.`'.MySqlLegacySupport::getInstance()->real_escape_string($this->_oListSettings->sqlData['module_list_cmsfieldname']).'` '.MySqlLegacySupport::getInstance()->real_escape_string($this->_oListSettings->sqlData['sort_order_direction']);
            }

            // check if categories are set
            $sCatQuery = "SELECT * FROM `module_list_cat` WHERE `cms_tpl_module_instance_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->instanceID)."'";
            $oModuleListCatList = TdbModuleListCatList::GetList($sCatQuery);
            if (0 == $oModuleListCatList->Length()) {
                $query = "SELECT `module_list`.*
                      FROM `module_list`
                 WHERE
                      `module_list`.`cms_tpl_module_instance_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->instanceID)."'
                  ORDER BY ".$secondarySort.'
                   ';
            } else {
                $query = "SELECT `module_list`.*, `module_list_cat`.`name` AS category_name, `module_list_cat`.`sort_order` AS category_sort_order
                      FROM `module_list`
                 LEFT JOIN `module_list_cat` ON `module_list`.`module_list_cat_id` = `module_list_cat`.`id`
                 WHERE
                      `module_list`.`cms_tpl_module_instance_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->instanceID)."'
                      AND `module_list_cat`.`cms_tpl_module_instance_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->instanceID)."'
                  ORDER BY `module_list_cat`.`sort_order`, ".$secondarySort.'
                   ';
            }

            /** @var $oModuleList TdbModuleListList */
            $oModuleList = TdbModuleListList::GetList($query);
            $this->oModuleListList = $oModuleList;
        }

        return $this->oModuleListList;
    }

    protected function LoadInstanceInfo()
    {
        $oModuleInstanceData = TdbCmsTplModuleInstance::GetNewInstance();
        $oModuleInstanceData->Load($this->instanceID);
        $this->_oModuleInstanceData = $oModuleInstanceData;

        $oListSettings = TdbModuleListConfig::GetNewInstance();
        $oListSettings->LoadFromField('cms_tpl_module_instance_id', $this->instanceID);
        $this->_oListSettings = $oListSettings;
        $this->data['oListSettings'] = $this->_oListSettings;
    }

    /**
     * if this function returns true, then the result of the execute function will be cached.
     *
     * @return bool
     */
    public function _AllowCache()
    {
        return true;
    }

    /**
     * return an assoc array of parameters that describe the state of the module.
     *
     * @return array
     */
    public function _GetCacheParameters()
    {
        $aParameters = parent::_GetCacheParameters();
        $aParameters['cms_tpl_module_instance_id'] = $this->instanceID;
        $aParameters['article'] = $this->global->GetUserData('article'.$this->instanceID);

        return $aParameters;
    }

    /**
     * if the content that is to be cached comes from the database (as ist most often the case)
     * then this function should return an array of assoc arrays that point to the
     * tables and records that are associated with the content. one table entry has
     * two fields:
     *   - table - the name of the table
     *   - id    - the record in question. if this is empty, then any record change in that
     *             table will result in a cache clear.
     *
     * @return array
     */
    public function _GetCacheTableInfos()
    {
        if (is_null($this->_oListSettings)) {
            $this->_oListSettings = new TCMSRecord();
            $this->_oListSettings->table = 'module_list_config';
            $this->_oListSettings->LoadFromField('cms_tpl_module_instance_id', $this->instanceID);
        }

        $tableInfo = array(array('table' => 'module_list_config', 'id' => $this->_oListSettings->id));
        $tableInfo[] = array('table' => 'module_list', 'id' => '');
        $tableInfo[] = array('table' => 'module_list_cat', 'id' => '');

        return $tableInfo;
    }
}
