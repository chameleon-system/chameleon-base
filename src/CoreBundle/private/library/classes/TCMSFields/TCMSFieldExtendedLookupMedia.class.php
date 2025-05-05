<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;

/**
 * The image pool.
 * /**/
class TCMSFieldExtendedLookupMedia extends TCMSFieldExtendedLookup
{
    public function GetReadOnly()
    {
        parent::GetReadOnly();

        return $this->getHtmlRenderedHtml(true);
    }

    public function GetHTML()
    {
        parent::GetHTML();

        return $this->getHtmlRenderedHtml(false);
    }

    /**
     * get html fore read only or edit mode.
     *
     * @param bool $bReadOnly
     *
     * @return string
     */
    protected function getHtmlRenderedHtml($bReadOnly)
    {
        $viewRenderer = $this->getViewRenderer();
        if (false === $bReadOnly) {
            $oCategory = $this->getDefaultMediaCategory();
            $viewRenderer->AddSourceObject('oCategory', $oCategory);
            $viewRenderer->AddSourceObject('bShowCategorySelector', $this->showCategorySelector());
        }
        $viewRenderer->AddSourceObject('bReadOnly', $bReadOnly);
        $mapperIdentifiers = $this->getMediaFieldMappers($bReadOnly);
        foreach ($mapperIdentifiers as $mapperIdentifier) {
            $viewRenderer->addMapperFromIdentifier($mapperIdentifier);
        }
        $viewRenderer->AddMapper(new TPkgCmsTextfieldImage());
        $this->oTableConf = $this->oTableRow->GetTableConf();
        $viewRenderer->AddSourceObject('sHtmlHiddenFields', $this->_GetHiddenField());
        $viewRenderer->AddSourceObject('sFieldName', $this->name);
        $viewRenderer->AddSourceObject('sTableId', $this->oTableConf->id);
        $viewRenderer->AddSourceObject('sRecordId', $this->recordId);
        $viewRenderer->AddSourceObject('iPosition', 0);
        $viewRenderer->AddSourceObject('emptyImageUrl', CHAMELEON_404_IMAGE_PATH_BIG);

        $imageId = $this->oTableRow->GetImageCMSMediaId(0, $this->name);
        if ('1' === $imageId) {
            $imageId = $this->getOriginalLanguageImageId();
        }

        $oImage = new TCMSImage();
        if (false === $oImage->Load($imageId)) {
            $oImage->Load(-1);
        }

        $iWidth = 0;
        $iHeight = 0;
        if (isset($oImage->aData['height'])) {
            $iHeight = (int) $oImage->aData['height'];
        }
        if (isset($oImage->aData['width'])) {
            $iWidth = (int) $oImage->aData['width'];
        }
        if (0 === $iHeight) {
            $iHeight = 150;
        }
        if (0 === $iWidth) {
            $iWidth = 150;
        }
        $viewRenderer->AddSourceObject('oImage', $oImage); // full image (not resized yet)
        $viewRenderer->AddSourceObject('sFullImageURL', $oImage->GetFullURL());
        // add +6 to width and height to get over zoom threshold to activate zoom light box
        $viewRenderer->AddSourceObject(
            'aTagProperties',
            [
                'width' => $iWidth + 6,
                'height' => $iHeight + 6,
                'cmsshowfull' => '1',
            ]
        );

        return $viewRenderer->Render('TCMSFieldMedia/mediaSingleItemUploader.html.twig', null, false);
    }

    /**
     * @param bool $isReadOnly
     *
     * @return string[]
     */
    protected function getMediaFieldMappers($isReadOnly)
    {
        $mapper = [];
        if (false === $isReadOnly) {
            $mapper[] = 'chameleon_system_core.mapper.media_field_upload';
        }
        $mapper[] = 'chameleon_system_core.mapper.media_field';
        $mapper[] = 'chameleon_system_core.mapper.media_field_image_box';

        return $mapper;
    }

    /**
     * @return string
     */
    protected function getOriginalLanguageImageId()
    {
        $row = $this->getDatabaseConnection()->fetchAssociative(
            'SELECT * FROM '.$this->getDatabaseConnection()->quoteIdentifier(
                $this->oTableRow->table
            ).' WHERE `id` = :id',
            ['id' => $this->oTableRow->id]
        );

        return $row[$this->name];
    }

    /**
     * @return ViewRenderer
     */
    protected function getViewRenderer()
    {
        return ServiceLocator::get('chameleon_system_view_renderer.view_renderer');
    }

    /**
     * @return TdbCmsMediaTree|null
     */
    protected function getDefaultMediaCategory()
    {
        $sCategoryId = $this->oDefinition->GetFieldtypeConfigKey('sDefaultCategoryId');
        $oCategory = TdbCmsMediaTree::GetNewInstance();
        if (false === $oCategory->Load($sCategoryId)) {
            $oCategory = null;
        }

        return $oCategory;
    }

    /**
     * set bShowCategorySelector=0 in field config to disable uploads.
     *
     * @return bool - default = true
     */
    protected function showCategorySelector()
    {
        $showCategorySelector = $this->oDefinition->GetFieldtypeConfigKey('bShowCategorySelector');

        return '0' !== $showCategorySelector;
    }

    /**
     * return an array of all js, css, or other header includes that are required
     * in the cms for this field. each include should be in one line, and they
     * should always be typed the same way so that no includes are included mor than once.
     *
     * @return array
     */
    public function GetCMSHtmlHeadIncludes()
    {
        $aIncludes = parent::GetCMSHtmlHeadIncludes();
        $aIncludes[] = $this->getFieldJSFunctions();

        return $aIncludes;
    }

    /**
     * @return string
     */
    protected function getFieldJSFunctions()
    {
        $aRequest = [
            'pagedef' => 'CMSUniversalUploader',
            'mode' => 'media',
            'callback' => 'CMSFieldPropertyTableCmsMediaPostUploadHook_'.$this->name,
        ];
        $aRequest['singleMode'] = '1';
        $parentField = $this->getInputFilterUtil()->getFilteredGetInput('field');
        if (null !== $parentField && '' !== $parentField) {
            $aRequest['parentIFrame'] = $parentField.'_iframe';
            $aRequest['parentIsInModal'] = $this->getInputFilterUtil()->getFilteredGetInput('isInModal', '');
        }

        $sURL = PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURLForJavascript($aRequest);
        $sErrorMessage = TGlobal::OutJS(ServiceLocator::get('translator')->trans('chameleon_system_core.field_document.error_missing_target'));
        $oGlobal = TGlobal::instance();

        $aParam = $oGlobal->GetUserData(null, ['module_fnc', '_rmhist', '_histid', '_fnc']);
        $aParam['pagedef'] = $oGlobal->GetUserData('pagedef');
        $aParam['module_fnc'] = ['contentmodule' => 'ExecuteAjaxCall'];
        $aParam['_fnc'] = 'connectImageObject';
        $aParam['callFieldMethod'] = '1';
        $aParam['_fieldName'] = $this->name;
        if (null !== $parentField && '' !== $parentField) {
            $aParam['parentIFrame'] = $parentField.'_iframe';
            $aParam['parentIsInModal'] = $this->getInputFilterUtil()->getFilteredGetInput('isInModal', '');
        }

        $sConnectImageURL = PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURLForJavascript($aParam);
        $aParam['_fnc'] = 'getNewImageTag';
        $sResetImageTag = PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURLForJavascript($aParam);

        $sHTML = <<<JAVASCRIPTCODE
<script type="text/javascript">
  function TCMSFieldPropertyTableCmsMediaOpenUploadWindow_{$this->name}(mediaTreeID, parentIFrame='') {
    if(mediaTreeID != '') {
      if(parentIFrame != '') {
        parent.CreateModalIFrameDialogCloseButton('{$sURL}&treeNodeID=' + mediaTreeID);
      } else {
        CreateModalIFrameDialogCloseButton('{$sURL}&treeNodeID=' + mediaTreeID);
      }
    } else {
      toasterMessage('{$sErrorMessage}','ERROR');
    }
  }
  function CMSFieldPropertyTableCmsMediaPostUploadHook_{$this->name}(data,responseMessage) {
    if (data) {
      sMediaOjectId = data;
      // now call method to insert object
      var sConnectImageURL = '{$sConnectImageURL}&cms_media_id='+encodeURIComponent(data);
      GetAjaxCallTransparent(sConnectImageURL, connectImageObject_{$this->name});
    } else {
      toasterMessage('Error',responseMessage);
    }
  }
  function connectImageObject_{$this->name}(data){
    GetAjaxCallTransparent('{$sResetImageTag}', resetImageTag_{$this->name});
  }
  function resetImageTag_{$this->name}(data){
        var imageDiv = $('#cmsimagefielditem_imagediv_{$this->name}0');
        var noImageDiv = $('#cmsimagefielditem_noimagediv_{$this->name}0');
        imageDiv.html(data['sHtml']);
        imageDiv.show();
        noImageDiv.hide();
        sFieldId = data['name'];
        $('#'+sFieldId).val(data['id']);
        initLightBox();
  }
</script>
JAVASCRIPTCODE;

        return $sHTML;
    }

    /**
     * sets methods that are allowed to be called via URL (ajax calls).
     */
    protected function DefineInterface()
    {
        parent::DefineInterface();
        $this->methodCallAllowed[] = 'connectImageObject';
        $this->methodCallAllowed[] = 'getNewImageTag';
    }

    /**
     * Save new image in record.
     *
     * @return string
     */
    public function connectImageObject()
    {
        $oGlobal = TGlobal::instance();
        $sMediaId = $oGlobal->GetUserData('cms_media_id');
        $oTableManager = TTools::GetTableEditorManager($this->sTableName, $this->recordId);
        $oTableManager->AllowEditByAll(true);
        if (true === $oTableManager->SaveField($this->name, $sMediaId)) {
            $sReturn = ServiceLocator::get('translator')->trans('chameleon_system_core.field_image_lookup.created');
        } else {
            $sReturn = ServiceLocator::get('translator')->trans('chameleon_system_core.field_image_lookup.error_creating');
        }
        $oTableManager->AllowDeleteByAll(false);

        return $sReturn;
    }

    /**
     * Get new uploaded image box html to replace the old.
     *
     * @return string
     */
    public function getNewImageTag()
    {
        $oViewRenderer = new ViewRenderer();
        $oViewRenderer->AddMapper(new TPkgCmsTextfieldImage());
        $oImage = $this->oTableRow->GetImage(0, $this->name, true);
        $iHeight = $oImage->aData['height'];
        $iWidth = $oImage->aData['width'];
        if (0 == $iHeight) {
            $iHeight = 150;
        }
        if (0 == $iWidth) {
            $iWidth = 150;
        }
        $oViewRenderer->AddSourceObject('oImage', $oImage); // full image (not resized yet)
        $oViewRenderer->AddSourceObject('sFullImageURL', $oImage->GetFullURL());
        $oViewRenderer->AddSourceObject(
            'aTagProperties',
            [
                'width' => $iWidth + 6,
                'height' => $iHeight + 6,
                'cmsshowfull' => '1',
            ]
        );
        $aData = [
            'id' => $oImage->id,
            'name' => $this->name,
            'sHtml' => $oViewRenderer->Render('/common/media/pkgCmsTextFieldImageResponsive.html.twig', null, false),
        ];

        return $aData;
    }

    /**
     * {@inheritdoc}
     */
    public function GetConnectedTableName()
    {
        return 'cms_media';
    }

    /**
     * {@inheritdoc}
     */
    public function toString()
    {
        return $this->data;
    }

    private function getInputFilterUtil(): InputFilterUtilInterface
    {
        return ServiceLocator::get('chameleon_system_core.util.input_filter');
    }
}
