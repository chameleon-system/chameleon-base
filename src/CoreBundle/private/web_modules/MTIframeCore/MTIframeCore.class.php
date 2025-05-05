<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class MTIframeCore extends TUserCustomModelBase
{
    protected $oTableRow;

    protected $bAllowHTMLDivWrapping = true;

    public function Execute()
    {
        $this->data = parent::Execute();
        $this->LoadTableRow();
        $this->data['instanceID'] = $this->instanceID;

        return $this->data;
    }

    protected function LoadTableRow()
    {
        $oIFrameConfig = TdbModuleIframe::GetNewInstance();
        $oIFrameConfig->LoadFromField('cms_tpl_module_instance_id', $this->instanceID);
        $this->data['sAdditionalHtml'] = $oIFrameConfig->GetTextField('additional_html');
        $this->data['oTableRow'] = $oIFrameConfig;
        $this->oTableRow = $oIFrameConfig;
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
     * if the content that is to be cached comes from the database (as is most often the case)
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
        $tableInfo = [['table' => 'module_iframe', 'id' => $this->oTableRow->id]];

        return $tableInfo;
    }
}
