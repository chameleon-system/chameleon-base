<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class MTFAQListCore extends TUserCustomModelBase
{
    protected $bAllowHTMLDivWrapping = true;

    public function Execute()
    {
        parent::Execute();

        if (TTools::FieldExists('module_faq', 'cms_tpl_module_instance_id')) {
            $sQuery = "SELECT * FROM `module_faq` WHERE `module_faq`.`cms_tpl_module_instance_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->instanceID)."'";
            $oList = TdbModuleFaqList::GetList($sQuery);
        } else {
            $oList = TdbModuleFaqList::GetList();
        }
        $this->ChangeOrderByForList($oList);
        $this->data['oItemList'] = $oList;

        return $this->data;
    }

    /**
     * change order by for the list - default is qdescription field in ascending order.
     *
     * @param TdbModuleFaqList $oList
     */
    protected function ChangeOrderByForList($oList)
    {
        $oList->ChangeOrderBy(['qdescription' => 'ASC']);
    }

    public function GetHtmlHeadIncludes()
    {
        $aIncludes = parent::GetHtmlHeadIncludes();

        if (file_exists(PATH_USER_CMS_PUBLIC.'/web_modules/MTFAQListCore/MTFAQListCore.css')) {
            $aIncludes[] = '<link rel="stylesheet" type="text/css" href="'.TGlobal::GetStaticURLToWebLib('/web_modules/MTFAQListCore/MTFAQListCore.css').'" />';
        }

        return $aIncludes;
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
        return [['table' => 'module_faq', 'id' => '']];
    }

    public function _GetCacheParameters()
    {
        return parent::_GetCacheParameters();
    }
}
