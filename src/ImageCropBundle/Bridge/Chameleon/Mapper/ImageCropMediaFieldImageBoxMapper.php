<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ImageCropBundle\Bridge\Chameleon\Mapper;

use AbstractViewMapper;
use ChameleonSystem\CoreBundle\Interfaces\MediaManagerUrlGeneratorInterface;
use ChameleonSystem\CoreBundle\Util\InputFilterUtil;
use IMapperCacheTriggerRestricted;
use IMapperRequirementsRestricted;
use IMapperVisitorRestricted;
use TGlobal;

class ImageCropMediaFieldImageBoxMapper extends AbstractViewMapper
{
    public function __construct(
        private readonly MediaManagerUrlGeneratorInterface|null $mediaManagerUrlGenerator,
        private readonly InputFilterUtil $inputFilterUtil,
    )
    {
    }

    /**
     * {@inheritdoc}
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements)
    {
        $oRequirements->NeedsSourceObject('sFieldName', 'string');
        $oRequirements->NeedsSourceObject('iPosition', 'int');
    }

    /**
     * {@inheritdoc}
     */
    public function Accept(
        IMapperVisitorRestricted $oVisitor,
        $bCachingEnabled,
        IMapperCacheTriggerRestricted $oCacheTriggerManager
    ) {
        $fieldName = $oVisitor->GetSourceObject('sFieldName');
        $position = $oVisitor->GetSourceObject('iPosition');
        $parentField = $this->inputFilterUtil->getFilteredGetInput('field');
        $url = $this->mediaManagerUrlGenerator->getUrlToPickImage('parent.setImageWithCrop', true);
        $js = "
            var width = $(window).width() - 50;
            saveCMSRegistryEntry('_currentFieldName', '" . TGlobal::OutJS($fieldName) . "');
            saveCMSRegistryEntry('_currentPosition', '" . TGlobal::OutJS($position) . "');
        ";

        if (null !== $parentField && '' !== $parentField) {
            $parentIFrame = $parentField . '_iframe';
            $extensionUrl = '&parentIFrame=' . $parentIFrame;
            $js .= "var url = '" . TGlobal::OutJS($url) . "';
                    var openFromParent = true;
                    var selfIFrame = window.frameElement;
                    if (selfIFrame && selfIFrame.parentNode?.classList.contains('modal-body')) {
                        openFromParent = false;
                    }
                    if (openFromParent) {
                        url = url.replace('parent.setImageWithCrop', 'setImageWithCrop') + '" . TGlobal::OutJS($extensionUrl). "';
                        saveCMSRegistryEntry('_parentIFrame','" . TGlobal::OutJS($parentIFrame) . "');
                        parent.CreateModalIFrameDialogCloseButton(url,width,0);
                    } else {
                        CreateModalIFrameDialogCloseButton(url,width,0);
                    }
            ";
        } else {
            $js .= "CreateModalIFrameDialogCloseButton('".TGlobal::OutJS($url)."',width,0);";
        }

        $oVisitor->SetMappedValue('sOpenWindowJSSetImage', $js);
    }
}
