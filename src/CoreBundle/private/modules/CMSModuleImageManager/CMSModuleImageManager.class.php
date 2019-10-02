<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class CMSModuleImageManager extends TCMSModelBase
{
    protected $oTableConf = null;
    protected $oTable = null;
    protected $oFieldDefinition = null;
    protected $nodeID = 1;
    protected $fieldName = null;
    protected $rootTreeID = 1;
    protected $imageTableConfId = null;

    public function &Execute()
    {
        parent::Execute();
        $mode = $this->global->GetUserData('_mode');
        if ('set' === $mode) {
            $this->SetImage();
        } else {
            $this->nodeID = $this->global->GetUserData('cms_media_tree_id');
            $oRootNode = new TCMSMediaManagerTreeNode();
            $oRootNode->Load($this->rootTreeID);
            $oImageTableConf = new TCMSTableConf();
            /** @var $oTable TCMSTableConf */
            $oImageTableConf->LoadFromField('name', 'cms_media');
            $this->imageTableConfId = $oImageTableConf->sqlData['id'];
            $this->data['treeHTML'] = '';
            $this->data['treePathHTML'] = '';

            $this->RenderTree($oRootNode, $this->nodeID, $this->fieldName);
            $this->_LoadImageLibrary();
            $this->SetTemplate('CMSModuleImageManager', 'selectimage');
        }

        return $this->data;
    }

    /**
     * defines an array of methods that may be called from outsite (ajax).
     */
    protected function DefineInterface()
    {
        parent::DefineInterface();
        $externalFunctions = array('SetImage', 'ClearImage');
        $this->methodCallAllowed = array_merge($this->methodCallAllowed, $externalFunctions);
    }

    /**
     * load the list object for existing module instances so the user can choose
     * one from the list and place it into a slot.
     */
    protected function _LoadImageLibrary()
    {
        // need to pass the parameters (modulespotname) back to the view
        $oListTable = new TCMSListManagerMediaSelector();
        /** @var $oListTable TCMSListManagerMediaSelector */
        if ($this->global->UserDataExists('sRestriction')) {
            $oListTable->sRestriction = $this->global->GetUserData('sRestriction');
        }

        // the image list must show only items that match the default value for the current position...
        // so, load the field def, get the image id @ the position, and use it to lookup the correct image size

        $imagefieldname = $this->global->GetUserData('imagefieldname');
        $tableId = $this->global->GetUserData('tableid');
        $position = $this->global->GetUserData('position');
        $id = $this->global->GetUserData('id');
        if (!empty($tableId)) {
            $this->_LoadData($tableId, $id, $imagefieldname);
        }

        $defaults = explode(',', $this->oFieldDefinition->sqlData['field_default_value']);
        $oImage = new TCMSImage();
        /** @var $oImage TCMSImage */
        $oImage->Load($defaults[$position]);

        $oListTable->Init($oImage);
        $list = $oListTable->GetList();
        $this->data['sTable'] = $list;

        return $list;
    }

    /**
     * @return array
     */
    public function ClearImage()
    {
        $imagefieldname = $this->global->GetUserData('imagefieldname');
        $tableId = $this->global->GetUserData('tableid');
        $position = $this->global->GetUserData('position');
        $id = $this->global->GetUserData('id');
        $imageField = $this->global->GetUserData('imagefieldvalue');

        $this->_LoadData($tableId, $id, $imagefieldname);

        $images = explode(',', $imageField);
        // get default value
        $defaults = explode(',', $this->oFieldDefinition->sqlData['field_default_value']);
        $images[$position] = $defaults[$position];

        $imageFieldContent = implode(',', $images);

        $returnData['fieldname'] = $imagefieldname;
        $returnData['imageFieldContent'] = $imageFieldContent;
        $returnData['imagePosition'] = $position;

        return $returnData;
    }

    /**
     * is called via ajax and generates the image box.
     *
     * @return string
     */
    public function SetImage()
    {
        $imageId = $this->global->GetUserData('imageid');
        $imagefieldname = $this->global->GetUserData('imagefieldname');
        $tableId = $this->global->GetUserData('tableid');
        $position = $this->global->GetUserData('position');
        $id = $this->global->GetUserData('id');
        $imageField = $this->global->GetUserData('imagefieldvalue');

        $this->_LoadData($tableId, $id, $imagefieldname);

        $images = explode(',', $imageField);
        // get default value
        $images[$position] = $imageId;

        $returnData = array();
        $returnData['fieldvalue'] = implode(',', $images);
        $oImage = new TCMSImage();
        /** @var $oImage TCMSImage */
        if ($oImage->Load($imageId)) {
            $maxThumbWidth = 140;
            $returnData['message'] = '';
            $returnData['messageType'] = '';
            $returnData['uniqueID'] = $oImage->uniqueID;
            $returnData['maxThumbWidth'] = $maxThumbWidth;

            $returnData['isFlashVideo'] = false;
            $sImageType = $oImage->GetImageType();
            if ('flv' == $sImageType || 'f4v' == $sImageType) {
                $returnData['isFlashVideo'] = true;
            }

            $returnData['sImage'] = $this->getBackendResponsiveThumbnail($oImage);
            $returnData['FLVPlayerURL'] = $oImage->FLVPlayerURL;
            $returnData['FLVPlayerHeight'] = $oImage->FLVPlayerHeight;
        } else {
            $returnData['message'] = TGlobal::Translate('chameleon_system_core.cms_module_image_manager.selected_image_not_found');
            $returnData['messageType'] = 'ERROR';
        }

        return $returnData;
    }

    /**
     * @param TCMSImage $image
     *
     * @return string
     */
    protected function getBackendResponsiveThumbnail(TCMSImage $image)
    {
        $viewRenderer = new ViewRenderer();
        $viewRenderer->AddMapper(new TPkgCmsTextfieldImage());
        $height = $image->aData['height'];
        $width = $image->aData['width'];
        if (0 === $height) {
            $height = 150;
        }
        if (0 === $width) {
            $width = 150;
        }
        $viewRenderer->AddSourceObject('oImage', $image);
        $viewRenderer->AddSourceObject('sFullImageURL', $image->GetFullURL());
        $viewRenderer->AddSourceObject(
            'aTagProperties',
            array(
                'width' => $width + 6,
                'height' => $height + 6,
                'cmsshowfull' => '1',
            )
        );

        return $viewRenderer->Render('/common/media/pkgCmsTextFieldImageResponsive.html.twig', null, false);
    }

    /**
     * Add messages to return data if active user cant use media file because media file was in
     * workflow transaction.
     * Returns true is user can use image and false otherwise.
     *
     * @param array $returnData
     * @param array $aImageData
     *
     * @return bool
     *
     * @deprecated since 6.2.0 - workflow is not supported anymore
     */
    protected function HandleWorkflowOnSetImage(&$returnData, $aImageData)
    {
        return true;
    }

    protected function _LoadData($tableId, $recordId, $imageFieldName)
    {
        $this->oTableConf = new TCMSTableConf();
        /** @var $oTableConf TCMSTableConf */
        $this->oTableConf->Load($tableId);
        $this->oFieldDefinition = $this->oTableConf->GetFieldDefinition($imageFieldName);
        $this->oTable = new TCMSRecord();
        /** @var $oTable TCMSRecord */
        $this->oTable->table = $this->oTableConf->sqlData['name'];
        $this->oTable->Load($recordId);
    }

    /**
     * Render Tree for Media Categories.
     *
     * @param TCMSTreeNode $oNode
     * @param int          $activeID
     * @param string       $fieldName
     * @param string       $path
     * @param int          $level
     */
    public function RenderTree(&$oNode, $activeID, $fieldName, $path = '', $level = 0)
    {
        $sNodeName = $oNode->GetName();
        if (!empty($sNodeName)) {
            $sCurrentNodeId = $oNode->id;
            if ((string) $sCurrentNodeId == (string) $this->rootTreeID) {
                $sCurrentNodeId = '';
            }

            $spacer = '';
            for ($i = 0; $i < $level; ++$i) {
                $spacer .= '  ';
            }

            ++$level;
            $this->data['treeHTML'] .= $spacer.'<li id="node'.$oNode->sqlData['cmsident'].'">';

            $activeStyle = '';
            if ((string) $activeID == (string) $oNode->id) {
                $activeStyle = ' class="active"';
            }

            $sListURL = $this->GetListURL();
            $sListURL .= '&amp;cms_media_tree_id='.$sCurrentNodeId;

            $this->data['treeHTML'] .= '<a href="'.$sListURL.'">'.$sNodeName;

            if ((string) $activeID == (string) $oNode->id) {
                $this->data['treeHTML'] .= '<span style="background: url('.TGlobal::GetPathTheme().'/images/icons/tick.png); height: 16px; background-repeat: no-repeat;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
            }

            $this->data['treeHTML'] .= '</a>';

            $oChildren = &$oNode->GetChildren(true);
            $iChildrenCount = $oChildren->Length();
            if ($iChildrenCount > 0) {
                $this->data['treeHTML'] .= "\n".$spacer."  <ul>\n";
            }
            $childcount = 0;
            while ($oChild = $oChildren->Next()) {
                $this->RenderTree($oChild, $activeID, $fieldName, $path, $level);
                ++$childcount;
            }
            if ($iChildrenCount > 0) {
                $this->data['treeHTML'] .= $spacer."</ul>\n";
            }

            $this->data['treeHTML'] .= "</li>\n";
        }
    }

    protected function GetListURL()
    {
        $sPosition = $this->global->GetUserData('position');
        $sImageFieldname = $this->global->GetUserData('imagefieldname');
        $sTableId = $this->global->GetUserData('tableid');
        $sId = $this->global->GetUserData('id');

        $aExternalListParams = $this->GetAdditionalListParams();
        if (!is_array($aExternalListParams)) {
            $aExternalListParams = array();
        }
        $aExternalListParams['_user_data'] = '';
        $aExternalListParams['_sort_order'] = '';
        $aExternalListParams['_listName'] = 'cmstablelistObj'.$this->imageTableConfId;
        $aExternalListParams['pagedef'] = 'CMSImageManagerLoadImage';
        $aExternalListParams['imagefieldname'] = $sImageFieldname;
        $aExternalListParams['tableid'] = $sTableId;
        $aExternalListParams['id'] = $sId;
        $aExternalListParams['position'] = $sPosition;
        $aExternalListParams['category'] = '';
        $aExternalListParams['_startRecord'] = '0';

        $sListURL = PATH_CMS_CONTROLLER.'?'.str_replace('&amp;', '&', TTools::GetArrayAsURL($aExternalListParams));

        return $sListURL;
    }

    /**
     * loads additional list parameters like iMaxWidth, iMaxHeight, sAllowedFileTypes.
     *
     * @return array
     */
    protected function GetAdditionalListParams()
    {
        $aAdditionalListParams = array('imageWidth', 'imageHeight', 'sAllowedFileTypes');

        $aExternalListParams = array();

        foreach ($aAdditionalListParams as $sListParam) {
            if ($this->global->UserDataExists($sListParam)) {
                $aExternalListParams[$sListParam] = $this->global->GetUserData($sListParam);
            }
        }

        return $aExternalListParams;
    }

    public function GetHtmlHeadIncludes()
    {
        $aIncludes = array();
        // first the includes that are needed for the all fields
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/jquery-ui-1.12.1.custom/jquery-ui.js').'" type="text/javascript"></script>';
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/jquery/flash/flash.js').'" type="text/javascript"></script>';
        $aIncludes[] = '<link href="'.TGlobal::GetPathTheme().'/css/table.css" rel="stylesheet" type="text/css" />';
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/table.js').'" type="text/javascript"></script>';
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/jquery/cookie/jquery.cookie.js').'" type="text/javascript"></script>';

        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/jquery/jsTree/jquery.jstree.js').'" type="text/javascript"></script>';
//        $aIncludes[] = '<script src="'.TGlobal::GetStaticURL('/bundles/chameleonsystemcore/javascript/jsTree/3.3.8/jstree.js').'"></script>';
//        $aIncludes[] = sprintf('<link rel="stylesheet" href="%s">', TGlobal::GetStaticURL('/bundles/chameleonsystemcore/javascript/jsTree/3.3.8/themes/default/style.css'));

        $aIncludes[] = '<link href="'.TGlobal::GetPathTheme().'/css/tooltip.css" rel="stylesheet" type="text/css" />';

        return $aIncludes;
    }
}
