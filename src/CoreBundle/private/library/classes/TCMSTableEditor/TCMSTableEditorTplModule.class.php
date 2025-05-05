<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSTableEditorTplModule extends TCMSTableEditor
{
    protected function DeleteRecordReferences()
    {
        $sQueryInstances = "SELECT * FROM `cms_tpl_module_instance` WHERE `cms_tpl_module_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->sId)."'";
        $oCmsTplModuleInstanceList = TdbCmsTplModuleInstanceList::GetList($sQueryInstances);
        /** @var $oCmsTplModuleInstanceList TdbCmsTplModuleInstanceList */
        $sInstanceIDs = $oCmsTplModuleInstanceList->GetIdList('id', true);
        if (!empty($sInstanceIDs)) {
            // delete all "page => module instance" references that are based on the deleted module
            $iTableID = TTools::GetCMSTableId('cms_tpl_page_cms_master_pagedef_spot');
            $oTableEditor = new TCMSTableEditorManager();
            /** @var $oTableEditor TCMSTableEditorManager */
            $query = 'SELECT * FROM `cms_tpl_page_cms_master_pagedef_spot` WHERE `cms_tpl_module_instance_id` IN ('.$sInstanceIDs.')';
            $result = MySqlLegacySupport::getInstance()->query($query);

            while ($row = MySqlLegacySupport::getInstance()->fetch_assoc($result)) {
                $oTableEditor->Init($iTableID, $row['id']);
                $oTableEditor->Delete($row['id']);
            }

            $iModuleInstanceTableID = TTools::GetCMSTableId('cms_tpl_module_instance');
            $oTableEditorModuleInstance = new TCMSTableEditorManager();
            /* @var $oTableEditorModuleInstance TCMSTableEditorManager */
            // delete all module instances that are based on the deleted module
            while ($oCmsTplModuleInstance = $oCmsTplModuleInstanceList->Next()) {
                $oTableEditorModuleInstance->Init($iModuleInstanceTableID, $oCmsTplModuleInstance->id);
                $oTableEditorModuleInstance->Delete($oCmsTplModuleInstance->id);
            }
        }
        parent::DeleteRecordReferences();
    }
}
