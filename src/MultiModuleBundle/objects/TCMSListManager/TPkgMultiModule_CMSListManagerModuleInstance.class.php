<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgMultiModule_CMSListManagerModuleInstance extends TPkgMultiModule_CMSListManagerModuleInstanceAutoParent
{
    /**
     * @return void
     */
    public function AddFields()
    {
        parent::AddFields();
        ++$this->columnCount;

        $jsParas = $this->_GetRecordClickJavaScriptParameters();
        $siteText = TGlobal::Translate('chameleon_system_multi_module.text.set_items');
        $this->tableObj->AddHeaderField(array('id' => $siteText.'&nbsp;&nbsp;'), 'left', null, 1, false);
        $this->tableObj->AddColumn('id', 'left', array($this, 'CallBackTemplateEngineInstanceMultiModuleSetItem'), $jsParas, 1);
    }

    /**
     * returns the navigation breadcrumbs of the module instance.
     *
     * @param string $id
     * @param array  $row
     *
     * @return string
     */
    public function CallBackTemplateEngineInstanceMultiModuleSetItem($id, $row)
    {
        $pageString = '';

        /** @var $oMultiModuleSetItemList TdbPkgMultiModuleSetItemList */
        $oMultiModuleSetItemList = TdbPkgMultiModuleSetItemList::GetList();
        $oMultiModuleSetItemList->AddFilterString("`pkg_multi_module_set_item`.`cms_tpl_module_instance_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($id)."'");
        /** @var $oCmsTplPage TdbPkgMultiModuleSetItem */
        while ($oMultiModuleSetItem = $oMultiModuleSetItemList->Next()) {
            $oMultiModuleSet = $oMultiModuleSetItem->GetFieldPkgMultiModuleSet();
            $pageString .= '<div style="white-space: nowrap;"><h2 style="margin: 0px 0px 5px 0px;">'.TGlobal::OutHTML($oMultiModuleSet->GetName()).' (ID '.TGlobal::OutHTML($oMultiModuleSet->id).'):</h2>'.TGlobal::OutHTML($oMultiModuleSetItem->GetName()).' (ID '.TGlobal::OutHTML($oMultiModuleSetItem->id).')</div>';
        }

        return $pageString;
    }
}
