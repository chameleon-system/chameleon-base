<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Interfaces\MediaManagerUrlGeneratorInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use Symfony\Contracts\Translation\TranslatorInterface;

class TCMSMediaFieldImageBoxMapper extends AbstractViewMapper
{
    /**
     * @var UrlUtil
     */
    private $urlUtil;

    /**
     * @var MediaManagerUrlGeneratorInterface
     */
    private $mediaManagerUrlGenerator;

    public function __construct(?UrlUtil $urlUtil = null, ?MediaManagerUrlGeneratorInterface $mediaManagerUrlGenerator = null)
    {
        if (null === $urlUtil) {
            $this->urlUtil = ServiceLocator::get('chameleon_system_core.util.url');
        } else {
            $this->urlUtil = $urlUtil;
        }
        if (null === $mediaManagerUrlGenerator) {
            $this->mediaManagerUrlGenerator = ServiceLocator::get('chameleon_system_media_manager.url_generator');
        } else {
            $this->mediaManagerUrlGenerator = $mediaManagerUrlGenerator;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements): void
    {
        $oRequirements->NeedsSourceObject('oImage', 'TCMSImage');
        $oRequirements->NeedsSourceObject('sFieldName', 'string');
        $oRequirements->NeedsSourceObject('sTableId', 'string');
        $oRequirements->NeedsSourceObject('sRecordId', 'string');
        $oRequirements->NeedsSourceObject('iPosition', 'int');
        $oRequirements->NeedsSourceObject('bReadOnly', 'boolean');
    }

    /**
     * {@inheritdoc}
     */
    public function Accept(IMapperVisitorRestricted $oVisitor, $bCachingEnabled, IMapperCacheTriggerRestricted $oCacheTriggerManager): void
    {
        /** @var $oTextModuleConfiguration TdbCmsTblConf */
        $oImage = $oVisitor->GetSourceObject('oImage');
        $iPosition = $oVisitor->GetSourceObject('iPosition');
        $sFieldName = $oVisitor->GetSourceObject('sFieldName');
        $sRecordId = $oVisitor->GetSourceObject('sRecordId');
        $sTableId = $oVisitor->GetSourceObject('sTableId');
        $bReadOnly = $oVisitor->GetSourceObject('bReadOnly');
        $aImageBoxData = $this->GetImageBox($oImage, $iPosition, $bReadOnly, $sFieldName, $sTableId, $sRecordId);
        $aImageBoxData['sFieldName'] = $sFieldName;
        $aImageBoxData['sRecordId'] = $sRecordId;
        $aImageBoxData['sTableId'] = $sTableId;
        $oVisitor->SetMappedValueFromArray($aImageBoxData);
    }

    /**
     * renders one image slot.
     *
     * @param TCMSImage $oImage
     * @param int $position
     * @param bool $bReadOnly - set to true to disable the action buttons
     * @param string $sFieldName
     * @param string $sTableId
     * @param string $sRecordId
     *
     * @return string
     */
    protected function GetImageBox($oImage, $position, $bReadOnly, $sFieldName, $sTableId, $sRecordId)
    {
        $bImageIsSet = false;
        $aImageData = [];
        $aImageData['sDeleteVisibleType'] = 'hidden';
        $aImageData['sImageURL'] = $oImage->GetFullURL();
        $aImageData['sThumbImageURL'] = '';
        $aImageData['sImageURLBig'] = '';
        $aImageData['sImageTag'] = '';
        if (!empty($oImage->id) && ($oImage->id >= 1000 || !is_numeric($oImage->id))) {
            $bImageIsSet = true;
            $aImageData['sDeleteVisibleType'] = 'visible';
            $aImageData['sOpenWindowJSMediaDetail'] = $this->_GetOpenWindowJSDetail($position, $oImage->id);
        }
        $aImageData['bImageSet'] = $bImageIsSet;
        $aImageData['sOpenWindowJSSetImage'] = $this->_GetOpenWindowJSSetImage($position, $sFieldName, $sTableId, $sRecordId, $oImage);
        $aImageData['iPosition'] = $position;
        $aImageData['sImageIconURL'] = URL_CMS;
        $aImageData['bReadOnly'] = $bReadOnly;

        return $aImageData;
    }

    /**
     * {@inheritdoc}
     */
    protected function _GetOpenWindowJSSetImage($position, $sFieldName, $sTableId, $sRecordId, $oImage)
    {
        $parentField = $this->getInputFilterUtil()->getFilteredGetInput('field');
        $isInModal = $this->getInputFilterUtil()->getFilteredGetInput('isInModal', '');
        $url = $this->mediaManagerUrlGenerator->getUrlToPickImage('parent._SetImage', false, $sFieldName, $sTableId, $sRecordId, $position);
        $js = "
            var width = $(window).width() - 50;
            saveCMSRegistryEntry('_currentFieldName', '".TGlobal::OutJS($sFieldName)."');
            saveCMSRegistryEntry('_currentPosition', '".TGlobal::OutJS($position)."');
        ";

        if (null !== $parentField && '' !== $parentField && '' === $isInModal) {
            $parentIFrame = $parentField.'_iframe';
            $extensionUrl = '&parentIFrame='.$parentIFrame;
            $js .= "var url = '".TGlobal::OutJS($url).TGlobal::OutJS($extensionUrl)."';
                    url = url.replace('parent._SetImage', '_SetImage');
                    saveCMSRegistryEntry('_parentIFrame', '".TGlobal::OutJS($parentIFrame)."');
                    parent.CreateModalIFrameDialogCloseButton(url, width, 0, '".TGlobal::OutJS($this->getTranslator()->trans('chameleon_system_core.field_media.select_dialog_title'))."');";
        } else {
            $js .= "CreateModalIFrameDialogCloseButton('".TGlobal::OutJS($url)."', width, 0, '".TGlobal::OutJS($this->getTranslator()->trans('chameleon_system_core.field_media.select_dialog_title'))."');";
        }

        return $js;
    }

    /**
     * renders the button that opens the image chooser popup.
     *
     * @param int $position
     * @param string $sRecordId
     *
     * @return string
     */
    protected function _GetOpenWindowJSDetail($position, $sRecordId)
    {
        static $internalCache = null;
        if (null !== $internalCache) {
            $oCmsTblConf = $internalCache;
        } else {
            $oCmsTblConf = TdbCmsTblConf::GetNewInstance();
            $oCmsTblConf->LoadFromField('name', 'cms_media');
            $internalCache = $oCmsTblConf;
        }
        $aParam = ['pagedef' => 'tableeditorPopup', 'id' => $sRecordId, 'tableid' => $oCmsTblConf->id, 'position' => $position];
        $url = $this->urlUtil->getArrayAsUrl($aParam, PATH_CMS_CONTROLLER.'?', '&');

        $parentField = $this->getInputFilterUtil()->getFilteredGetInput('field');
        $isInModal = $this->getInputFilterUtil()->getFilteredGetInput('isInModal', '');
        $js = 'var width=$(window).width() - 50;';
        if (null !== $parentField && '' !== $parentField && '' === $isInModal) {
            $parentIFrame = $parentField.'_iframe';
            $js .= "saveCMSRegistryEntry('_parentIFrame','".TGlobal::OutJS($parentIFrame)."');
                    parent.CreateModalIFrameDialogCloseButton('".TGlobal::OutJS($url)."',width,0,'".TGlobal::OutJS($this->getTranslator()->trans('chameleon_system_core.field_media.image_details_title'))."');";
        } else {
            $js .= "CreateModalIFrameDialogCloseButton('".TGlobal::OutJS($url)."',width,0,'".TGlobal::OutJS($this->getTranslator()->trans('chameleon_system_core.field_media.image_details_title'))."');";
        }

        return $js;
    }

    protected function GetMaxWidth($oImage)
    {
        return '';
    }

    protected function GetMaxHeight($oImage)
    {
        return '';
    }

    protected function GetAllowedFileTypes()
    {
        return '';
    }

    private function getTranslator(): TranslatorInterface
    {
        return ServiceLocator::get('translator');
    }

    private function getInputFilterUtil(): InputFilterUtilInterface
    {
        return ServiceLocator::get('chameleon_system_core.util.input_filter');
    }
}
