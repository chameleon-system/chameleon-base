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
 * Treemanagement Module for the CMS Navigation tree.
 * /**/
class CMSDocumentManagerTreeRPC extends CMSModulePageTreeRPC
{
    /**
     * called before the constructor, and before any external functions get called, but
     * after the constructor.
     */
    public function Init()
    {
        $this->treeTable = 'cms_document_tree';
        $this->treeContentTable = 'cms_document';
        $this->contentTable = null;
        $this->dbObjectCLass = 'TCMSDocumentManagerTreeNode';
        $this->onTitleClickCallBackFnc = 'showFileList';
    }

    /**
     * delete directory and all subdirectories
     * delete all files in these directories
     * delete all connections to files in these directories.
     */
    public function removeNode($directoryID = null)
    {
        if (is_null($directoryID)) {
            $directoryID = $this->rpcData->node->widgetId;
        }
        $sDocumentTreeTableEditorID = TTools::GetCMSTableId($this->treeTable);
        $oTableEditor = new TCMSTableEditorManager();
        /* @var $oTableEditor TCMSTableEditorManager */
        $oTableEditor->Init($sDocumentTreeTableEditorID, $directoryID);
        $oTableEditor->Delete($directoryID);
        // update cache
        $this->getCacheService()->callTrigger($this->treeTable, $directoryID);
        $this->UpdateSubtreePathCache($directoryID);

        return json_encode(true);
    }

    /**
     * callback function to create the node properties.
     */
    public function _nodeProperties($oTreeNode)
    {
        $child = [];

        $name = $oTreeNode->sqlData['name'];
        if (empty($name)) {
            $name = '['.TGlobal::Translate('chameleon_system_core.text.unnamed_record').']';
        }

        $child['title'] = '<span class="">'.TGlobal::OutHTML($name).'</span>';

        $child['isFolder'] = ($oTreeNode->CountChildren() > 0);
        $child['childIconSrc'] = '';
        $child['widgetId'] = $oTreeNode->id;
        $child['widgetType'] = 'TreeNode';
        $child['objectId'] = $oTreeNode->id;

        return $child;
    }

    public function getChildrenPortalCondition()
    {
        return '';
    }
}
