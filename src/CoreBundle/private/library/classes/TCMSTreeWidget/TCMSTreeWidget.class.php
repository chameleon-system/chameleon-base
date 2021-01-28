<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSTreeWidget
{
    /**
     * defines the SQL table name of the tree (cms_tree, cms_media_tree, cms_document_tree).
     *
     * @var string
     */
    protected $sTreeTableName = '';

    /**
     * defines the ajax page definition name (e.g. CMSDocumentManager).
     *
     * @var string
     */
    protected $sAjaxPageDef = '';

    /**
     * defines the ajax module spot name (e.g. content).
     *
     * @var string - default content
     */
    protected $sAjaxModuleSpot = 'content';

    /**
     * defines the database class name of the tree node (TdbCmsTree, TdbCmsMediaTree...)
     * based on TCMSTreeNode.
     *
     * @var string
     */
    protected $sTreeNodeClassName = '';

    /**
     * defines the levels that should be open on tree init.
     *
     * @var int
     */
    protected $iOpenNodesLevel = 2;

    /**
     * defines the root node ID (1 or 99 for navigation tree).
     *
     * @var string
     */
    protected $sRootNodeID = '1';

    /**
     * defines the context menu view name (filename without view.php extension).
     *
     * @var string
     */
    protected $sContextMenuView = '';

    /**
     * defines the context menu view path/directory (default is TCMSTreeWidget in /rendering/objectviews).
     *
     * @var string
     */
    protected $sContextMenuViewPath = 'TCMSTreeWidget';

    /**
     * defines the context menu view location (e.g. Core, Customer, Custom-Core).
     *
     * @var string
     */
    protected $sContextMenuViewType = 'Core';

    /**
     * defines the name of the root node.
     *
     * @var string
     */
    protected $sRootNodeName = 'Directories';

    /**
     * user has the right to add new nodes.
     *
     * @var bool
     */
    protected $bHasNewPermission = false;

    /**
     * user has the right to edit existing nodes.
     *
     * @var bool
     */
    protected $bHasEditPermission = false;

    /**
     * user has the right to delete nodes.
     *
     * @var bool
     */
    protected $bHasDeletePermission = false;

    /**
     * user has the right to upload images.
     *
     * @var bool
     */
    protected $bHasUploadPermission = false;

    /**
     * @param string $sTreeTableName
     */
    public function Init($sTreeTableName, $sAjaxPageDef, $sAjaxModuleSpot)
    {
        $this->sTreeTableName = $sTreeTableName;
        if (!empty($sTreeTableName)) {
            $this->sTreeNodeClassName = 'Tdb'.TCMSTableToClass::ConvertToClassString($sTreeTableName);
            $this->LoadTreeUserRights();
        }

        $this->sAjaxPageDef = $sAjaxPageDef;
        $this->sAjaxModuleSpot = $sAjaxModuleSpot;
    }

    /**
     * loads the table rights for the current CMS user (e.g. new, edit and delete permission)
     * the rights are loaded in class properties: $this->bHas[New|Edit|Delete]Permission.
     */
    protected function LoadTreeUserRights()
    {
        /** @var $oUser TdbCmsUser */
        $oUser = TdbCmsUser::GetActiveUser();
        $this->bHasNewPermission = $oUser->oAccessManager->HasNewPermission($this->sTreeTableName);
        $this->bHasEditPermission = $oUser->oAccessManager->HasEditPermission($this->sTreeTableName);
        $this->bHasDeletePermission = $oUser->oAccessManager->HasDeletePermission($this->sTreeTableName);
        $this->bHasUploadPermission = $oUser->oAccessManager->functions->HasRight('media_edit');

        // TODO this is used from media manager and document manager (so media_edit is the right role)?
    }

    /**
     * sets the context menu view name (filename without view.php extension).
     *
     * @param string $sViewName
     * @param string $sViewPath - default is TCMSTreeWidget in /rendering/objectviews
     * @param string $sViewType - Core,Customer,Custom-Core
     */
    public function SetContextMenuView($sContextMenuView, $sViewPath = 'TCMSTreeWidget', $sViewType = 'Core')
    {
        $this->sContextMenuView = $sContextMenuView;
        $this->sContextMenuViewPath = $sViewPath;
        $this->sContextMenuViewType = $sViewType;
    }

    /**
     * sets the name of the root node.
     *
     * @param string $sRootNodeName
     */
    public function SetRootNodeName($sRootNodeName)
    {
        $this->sRootNodeName = $sRootNodeName;
    }

    /**
     * sets the open tree levels.
     *
     * @param int $iOpenNodesLevel | default 2
     */
    public function SetOpenNodesLevel($iOpenNodesLevel = 2)
    {
        $this->iOpenNodesLevel = (int) $iOpenNodesLevel;
    }

    /**
     * sets the root node ID.
     *
     * @param string $sRootNodeID | default 1
     */
    public function SetRootNodeID($sRootNodeID = '1')
    {
        $this->sRootNodeID = $sRootNodeID;
    }

    /**
     * loads sub nodes of a tree node
     * is called via ajax.
     *
     * @return array
     */
    public function GetChildren($sNodeID)
    {
        $aReturnVal = array();
        if (!empty($sNodeID)) {
            /** @var $oTreeNode TCMSTreeNode */
            $oTreeNode = call_user_func(array($this->sTreeNodeClassName, 'GetNewInstance'), null);
            if ($oTreeNode->Load($sNodeID)) {
                $iMaxLevel = 1;
                if ($sNodeID == $this->sRootNodeID) {
                    $iMaxLevel = $this->iOpenNodesLevel;
                }
                $cmsUser = TCMSUser::GetActiveUser();
                $editLanguageId = $cmsUser->GetCurrentEditLanguageID();
                $aReturnVal = $this->RenderJSONNodes($oTreeNode, $iMaxLevel, 0, $editLanguageId);
            }
        }

        return $aReturnVal;
    }

    /**
     * @param TCMSTreeNode $oTreeNode
     * @param int          $iMaxLevel      - maximum level to render
     * @param int          $iCurrentLevel  - current render level
     * @param null         $editLanguageId
     *
     * @return array
     */
    protected function RenderJSONNodes($oTreeNode, $iMaxLevel = 1, $iCurrentLevel = 0, $editLanguageId = null)
    {
        $bRootLevelCall = false;
        if (0 == $iCurrentLevel) {
            $aReturnVal = array();
            $bRootLevelCall = true;
        }

        if (is_object($oTreeNode)) {
            $sState = 'leaf';

            if (null !== $editLanguageId) {
                $oTreeNode->SetLanguage($editLanguageId);
            }
            $sNodeName = $oTreeNode->GetName();
            if (empty($sNodeName)) {
                $sNodeName = TGlobal::Translate('chameleon_system_core.text.unnamed_record');
            }

            if ($oTreeNode->id == $this->sRootNodeID && !empty($this->sRootNodeName)) {
                $sNodeName = $this->sRootNodeName;
            }

            // check if node has children
            $oChildrenNodeList = $oTreeNode->GetChildren();
            $iChildNodes = $oChildrenNodeList->Length();
            if ($iChildNodes > 0) {
                if ($iMaxLevel > 1 && $iCurrentLevel < $iMaxLevel) { // last rendered level gets open state if it has child nodes
                    $sState = 'open';
                } else {
                    $sState = 'closed';
                }
            }

            $aHTMLAttributes = array('id' => 'node'.TGlobal::OutJS($oTreeNode->id));
            $aHTMLAttributes = $this->GetNodeHTMLAttributes($aHTMLAttributes, $oTreeNode);

            $aNodeProperties = array('attr' => $aHTMLAttributes, 'data' => $sNodeName, 'state' => $sState);

            if ($iCurrentLevel <= $iMaxLevel) {
                ++$iCurrentLevel;
                $aSubNodes = array();
                while ($oChildrenNode = $oChildrenNodeList->Next()) {
                    $aSubNodes[] = $this->RenderJSONNodes($oChildrenNode, $iMaxLevel, $iCurrentLevel++, $editLanguageId);
                }

                if ($bRootLevelCall) { // root node init call or sub nodes ajax call
                    if ($oTreeNode->id == $this->sRootNodeID) { // root node init call, render root node
                        $aNodeProperties['children'] = $aSubNodes;
                    } else {
                        $aNodeProperties = $aSubNodes; // replace root node with sub nodes
                    }
                } else { //sub level call
                    $aNodeProperties['children'] = $aSubNodes;
                }
            }

            $aReturnVal[] = $aNodeProperties;
        }

        return $aReturnVal;
    }

    /**
     * returns an array of HTML attributes
     * sets a rel attribute with rootNode and permission flags.
     *
     * @param array        $aHTMLAttributes
     * @param TCMSTreeNode $oTreeNode
     *
     * @return array $aHTMLAttributes
     */
    protected function GetNodeHTMLAttributes($aHTMLAttributes, $oTreeNode)
    {
        $aHTMLAttributes['rel'] = '';
        if ($oTreeNode->id == $this->sRootNodeID) {
            $aHTMLAttributes['rel'] = 'rootNode';
        }
        if ($this->bHasNewPermission) {
            $aHTMLAttributes['rel'] = $aHTMLAttributes['rel'] .= ' bHasNewPermission';
        }
        if ($this->bHasEditPermission) {
            $aHTMLAttributes['rel'] = $aHTMLAttributes['rel'] .= ' bHasEditPermission';
        }
        if ($this->bHasDeletePermission) {
            $aHTMLAttributes['rel'] = $aHTMLAttributes['rel'] .= ' bHasDeletePermission';
        }
        if ($this->bHasUploadPermission) {
            $aHTMLAttributes['rel'] = $aHTMLAttributes['rel'] .= ' bHasUploadPermission';
        }

        return $aHTMLAttributes;
    }

    /**
     * deletes a node.
     *
     * @return bool|string - returns the deleted node id on success
     */
    public function DeleteNode($sNodeID)
    {
        $bReturnVal = false;
        $iTableID = TTools::GetCMSTableId($this->sTreeTableName);
        /** @var $oTableEditorManager TCMSTableEditorManager */
        $oTableEditorManager = new TCMSTableEditorManager();
        if ($oTableEditorManager->Init($iTableID, $sNodeID)) {
            if ($oTableEditorManager->Delete($sNodeID)) {
                $bReturnVal = $sNodeID;
            }
        }

        return $bReturnVal;
    }

    /**
     * renames a node.
     *
     * @return stdClass
     */
    public function RenameNode($sNodeID, $sNewTitle)
    {
        $oReturnObj = new stdClass();
        $oReturnObj->status = false;

        if (!empty($sNodeID) && !empty($sNewTitle)) {
            $iTableID = TTools::GetCMSTableId($this->sTreeTableName);
            /** @var $oTableEditorManager TCMSTableEditorManager */
            $oTableEditorManager = new TCMSTableEditorManager();
            $oTableEditorManager->Init($iTableID, $sNodeID);
            // $oTableEditorManager->AllowEditByAll();

            if ($oTableEditorManager->SaveField('name', $sNewTitle, true)) {
                $oReturnObj->status = true;
            }
        }

        return $oReturnObj;
    }

    /**
     * moves node and all subnodes.
     *
     * @return stdClass
     */
    public function MoveNode($nodeID, $newParentNodeID, $newIndex = false)
    {
        if (!$newIndex) {
            $newIndex = 0;
        }

        $iTableID = TTools::GetCMSTableId($this->sTreeTableName);
        /** @var $oTableEditorManager TCMSTableEditorManager */
        $oTableEditorManager = new TCMSTableEditorManager();

        if (0 == $newIndex) {
            $query = 'SELECT * FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sTreeTableName).'`
                   WHERE `entry_sort` >= '.MySqlLegacySupport::getInstance()->real_escape_string($newIndex)."
                     AND `parent_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($newParentNodeID)."'";

            $oTreeNodeList = call_user_func(array($this->sTreeNodeClassName.'List', 'GetList'), $query);
            while ($oTreeNode = $oTreeNodeList->Next()) {
                $oTableEditorManager->Init($iTableID, $oTreeNode->id);
                // $oTableEditorManager->AllowEditByAll();
                $oTableEditorManager->SaveField('entry_sort', $oTreeNode->fieldEntrySort + 1);
            }
        } else {
            $query = 'SELECT * FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($this->sTreeTableName)."` WHERE `parent_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($newParentNodeID)."' AND `id` != '".MySqlLegacySupport::getInstance()->real_escape_string($nodeID)."' ORDER BY `entry_sort`  ASC";

            $oTreeNodeList = call_user_func(array($this->sTreeNodeClassName.'List', 'GetList'), $query);
            $count = 0;
            while ($oTreeNode = $oTreeNodeList->Next()) {
                if ($newIndex == $count) {
                    ++$count;
                }

                $oTableEditorManager->Init($iTableID, $oTreeNode->id);
                // $oTableEditorManager->AllowEditByAll();
                $oTableEditorManager->SaveField('entry_sort', $count);
                ++$count;
            }
        }

        $oTableEditorManager->Init($iTableID, $nodeID);
        // $oTableEditorManager->AllowEditByAll();
        $oTableEditorManager->SaveField('entry_sort', $newIndex);
        $oTableEditorManager->SaveField('parent_id', $newParentNodeID, true);

        $oReturnObj = new stdClass();
        $oReturnObj->status = true;
        $oReturnObj->id = $nodeID;

        return $oReturnObj;
    }

    /**
     * returns array of head includes.
     *
     * @return array
     */
    public function GetHtmlHeadIncludes()
    {
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURL(URL_CMS.'javascript/jquery/cookie/jquery.cookie.js').'" type="text/javascript"></script>';
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURL(URL_CMS.'javascript/jquery/jsTree/jquery.jstree.js').'" type="text/javascript"></script>';

        $aIncludes[] = '<style type="text/css">
      .jstree-default.jstree-focused {
        background: none !important;
      }
      </style>';

        $oViewParser = new TViewParser();
        $oViewParser->bShowTemplatePathAsHTMLHint = false;
        $oViewParser->AddVar('sAjaxModuleSpot', $this->sAjaxModuleSpot);
        $oViewParser->AddVar('sAjaxPageDef', $this->sAjaxPageDef);
        $oViewParser->AddVar('sRootNodeID', $this->sRootNodeID);

        $sContextMenuItems = '';
        if (!empty($this->sContextMenuView)) {
            $sContextMenuItems = $oViewParser->RenderObjectView($this->sContextMenuView, $this->sContextMenuViewPath, $this->sContextMenuViewType);
        }

        $oViewParser->AddVar('sContextMenuItems', $sContextMenuItems);
        $aIncludes[] = $oViewParser->RenderObjectView('treeInit', 'TCMSTreeWidget', 'Core');

        return $aIncludes;
    }
}
