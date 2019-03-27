<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSTableEditorMasterPagedefSpot extends TCMSTableEditor
{
    protected function DeleteRecordReferences()
    {
        // delete the spot connections

        $oCmsTplPageCmsMasterPagedefSpotList = TdbCmsTplPageCmsMasterPagedefSpotList::GetList($query);
        /** @var $oCmsTplPageCmsMasterPagedefSpotList TdbCmsTplPageCmsMasterPagedefSpotList */
        $iTableID = TTools::GetCMSTableId('cms_tpl_page_cms_master_pagedef_spot');
        $oTableEditor = new TCMSTableEditorManager();
        /** @var $oTableEditor TCMSTableEditorManager */
        while ($oCmsTplPageCmsMasterPagedefSpot = $oCmsTplPageCmsMasterPagedefSpotList->Next()) {
            /** @var $oCmsTplPageCmsMasterPagedefSpot TdbCmsTplPageCmsMasterPagedefSpot */
            $oTableEditor->Init($iTableID, $oCmsTplPageCmsMasterPagedefSpot->id);
            $oTableEditor->Delete($oCmsTplPageCmsMasterPagedefSpot->id);
        }

        parent::DeleteRecordReferences();
    }
}
