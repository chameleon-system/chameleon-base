<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class CMSModuleWYSIWYGImage extends TCMSModelBase
{
    /**
     * @deprecated since 6.3.0 - not used anymore
     */
    protected $oMediaTreeNode;

    /**
     * the current tree node.
     *
     * @var int
     */
    protected $directoryID;

    /**
     * {@inheritdoc}
     */
    public function Init()
    {
        $this->directoryID = $this->global->GetUserData('directoryID');
    }

    public function Execute()
    {
        $this->data = parent::Execute();

        $this->data['maxUploadSize'] = TTools::getUploadMaxSize();

        $this->GetMediaTreeSelectBox();

        $oImageTableConf = new TCMSTableConf();
        /* @var $oTable TCMSTableConf */
        $oImageTableConf->LoadFromField('name', 'cms_media');
        $this->data['id'] = $oImageTableConf->sqlData['id'];
        $this->data['cmsident'] = $oImageTableConf->sqlData['cmsident'];
        $this->data['sAllowedFileTypes'] = $this->global->GetUserData('sAllowedFileTypes');
        $this->data['CKEditor'] = $this->global->GetUserData('CKEditor');
        $this->data['CKEditorFuncNum'] = $this->global->GetUserData('CKEditorFuncNum');
        $this->data['langCode'] = $this->global->GetUserData('langCode');

        return $this->data;
    }

    /**
     * generates a select box with the media tree.
     */
    protected function GetMediaTreeSelectBox()
    {
        $html = '';
        $oTreeSelect = new TCMRenderMediaTreeSelectBox();
        $html .= $oTreeSelect->GetTreeOptions(null, true);

        $this->data['mediaTreeSelectBox'] = $html;
    }

    public function DefineInterface()
    {
        parent::DefineInterface();
        $externalFunctions = ['GetMediaProperties'];
        $this->methodCallAllowed = array_merge($this->methodCallAllowed, $externalFunctions);
    }

    /**
     * ajax callback to get the image properties.
     *
     * @return array
     */
    public function GetMediaProperties()
    {
        $mediaID = $this->global->GetUserData('mediaID');
        if (!empty($mediaID)) {
            $oImage = new TCMSImage();
            /* @var $oImage TCMSImage */
            $oImage->Load($mediaID);

            $oThumb = $oImage->GetThumbnail(100, 100);
            $sFileType = $oImage->GetImageType();
            $sThumbURL = $oThumb->GetRelativeURL();
            if ($oImage->IsExternalMovie()) {
                $fullImageURL = $oImage->GetFullURL();
                $sThumbURL = $fullImageURL;
            } else {
                $fullImageURL = $oImage->GetRelativeURL();
            }

            $returnArray = [];
            $returnArray['image'] = $oImage;
            $returnArray['thumb'] = $oThumb;
            $sThumbURL = str_replace('&amp;', '&', $sThumbURL);
            $returnArray['thumbnail_url'] = $sThumbURL;
            $fullImageURL = str_replace('&amp;', '&', $fullImageURL);
            $returnArray['image_url'] = $fullImageURL;
            $returnArray['file_type'] = $sFileType;
            $returnArray['CKEditorFuncNum'] = $this->global->GetUserData('CKEditorFuncNum');

            return $returnArray;
        }
    }

    public function GetHtmlHeadIncludes()
    {
        $aIncludes = parent::GetHtmlHeadIncludes();
        // first the includes that are needed for the all fields
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/jquery-ui-1.12.1.custom/jquery-ui.js').'" type="text/javascript"></script>';

        return $aIncludes;
    }
}
