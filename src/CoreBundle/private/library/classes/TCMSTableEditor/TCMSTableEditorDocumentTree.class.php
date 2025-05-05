<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSTableEditorDocumentTree extends TCMSTableEditorTreeShared
{
    /**
     * remove all documents in folder $sFolderId.
     *
     * @param int $sFolderId - folder id
     */
    protected function DeleteFolderItems($sFolderId)
    {
        $sDocumentTableID = TTools::GetCMSTableId('cms_document');
        $oDocumentEditor = new TCMSTableEditorManager();
        /** @var $oMediaEditor TCMSTableEditorManager */
        $query = "SELECT * FROM `cms_document` WHERE `cms_document_tree_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sFolderId)."'";
        $oCmsDocumentList = TdbCmsDocumentList::GetList($query);
        /** @var $oCmsDocumentList TdbCmsDocumentList */
        while ($oCmsDocument = $oCmsDocumentList->Next()) {
            /* @var $oCmsDocument TdbCmsDocument */
            $oDocumentEditor->Init($sDocumentTableID, $oCmsDocument->id);
            $oDocumentEditor->Delete($oCmsDocument->id);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function Delete($sId = null)
    {
        if (null !== $sId) {
            $this->DeleteFolderItems($sId);
        }

        parent::Delete($sId);
    }
}
