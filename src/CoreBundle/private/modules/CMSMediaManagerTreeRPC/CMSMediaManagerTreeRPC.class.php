<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Doctrine\DBAL\Connection;

/**
 * Treemanagement Module for the CMS Navigation tree.
 *
 * @deprecated since 6.2.0 - Chameleon has a new media manager
 */
class CMSMediaManagerTreeRPC extends CMSModulePageTreeRPC
{
    /**
     * called before the constructor, and before any external functions get called, but
     * after the constructor.
     */
    public function Init()
    {
        $this->treeTable = 'cms_media_tree';
        $this->treeContentTable = 'cms_media';
        $this->contentTable = null;
        $this->dbObjectCLass = 'TCMSMediaManagerTreeNode';
        $this->onTitleClickCallBackFnc = 'showFileList';
    }

    /**
     * delete directory and all subdirectories
     * delete all files in these directories
     * delete all connections to files in these directories.
     */
    public function removeNode($directoryID = null)
    {
        // check if we have the right to delete the node
        $fileIDs = false;
        $oUser = &TCMSUser::GetActiveUser();
        if ($oUser->oAccessManager->HasDeletePermission('cms_media_tree')) {
            if (is_null($directoryID)) {
                $directoryID = $this->rpcData->node->widgetId;
            }

            $fileIDs = array();
            $iTableID = TTools::GetCMSTableId('cms_media');
            $oTableEditor = new TCMSTableEditorManager();
            /** @var $oTableEditor TCMSTableEditorMedia */
            $oMediaManagerTreeNode = new TCMSMediaManagerTreeNode($directoryID);
            $oFilesList = $oMediaManagerTreeNode->GetFilesInDirectory();
            if ($oFilesList) { // directory is filled
                while ($oFile = $oFilesList->Next()) {
                    /** @var $oFile TCMSMediaManagerTreeNode */
                    // delete file and all connections

                    $oTableEditor->Init($iTableID, $oFile->id);
                    if ($oTableEditor->Delete($oFile->id)) { // returns true if a connection was found and removed
                        $fileIDs[] = $oFile->id;
                    }
                }
            }

            $databaseConnection = $this->getDatabaseConnection();
            $quotedTreeTable = $databaseConnection->quoteIdentifier($this->treeTable);
            $quotedDirectoryId = $databaseConnection->quote($directoryID);

            // delete directory
            $query = "DELETE FROM $quotedTreeTable WHERE `id` = $quotedDirectoryId";
            MySqlLegacySupport::getInstance()->query($query);

            // go down the tree to all subnodes
            $query = "SELECT * FROM $quotedTreeTable WHERE `parent_id` = $quotedDirectoryId";
            $subNodes = MySqlLegacySupport::getInstance()->query($query);
            while ($subNode = MySqlLegacySupport::getInstance()->fetch_assoc($subNodes)) {
                $this->removeNode($subNode['id']);
            }

            TCacheManager::PerformeTableChange($this->treeTable, $directoryID);

            $fileIDs = true;
        }

        return json_encode($fileIDs);
    }

    /**
     * move node and all subnodes.
     */
    public function move()
    {
        $oUser = &TCMSUser::GetActiveUser();
        if ($oUser->oAccessManager->HasEditPermission('cms_media_tree')) {
            return parent::move();
        } else {
            return json_encode(false);
        }
    }

    /**
     * callback function to create the node properties.
     */
    public function _nodeProperties(&$oTreeNode)
    {
        $child = array();

        $child['title'] = '<span class="">'.TGlobal::OutHTML($oTreeNode->sqlData['name']).'</span>';

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

    /**
     * @return Connection
     */
    private function getDatabaseConnection()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('database_connection');
    }
}
