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
use IMapperCacheTriggerRestricted;
use IMapperRequirementsRestricted;
use IMapperVisitorRestricted;
use TGlobal;

class ImageCropMediaFieldImageBoxMapper extends AbstractViewMapper
{
    /**
     * @var MediaManagerUrlGeneratorInterface
     */
    private $mediaManagerUrlGenerator;

    /**
     * @param MediaManagerUrlGeneratorInterface|null $mediaManagerUrlGenerator
     */
    public function __construct(MediaManagerUrlGeneratorInterface $mediaManagerUrlGenerator)
    {
        $this->mediaManagerUrlGenerator = $mediaManagerUrlGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements): void
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
    ): void {
        $url = $this->mediaManagerUrlGenerator->getUrlToPickImage('parent.setImageWithCrop', true);
        $fieldName = $oVisitor->GetSourceObject('sFieldName');
        $position = $oVisitor->GetSourceObject('iPosition');

        $js = "var width=$(window).width() - 50; var height=$(window).height() - 100; saveCMSRegistryEntry('_currentFieldName','".TGlobal::OutJS(
                $fieldName
            )."');saveCMSRegistryEntry('_currentPosition','".TGlobal::OutJS(
                $position
            )."');CreateModalIFrameDialogCloseButton('".TGlobal::OutJS($url)."',width,height);";
        $oVisitor->SetMappedValue('sOpenWindowJSSetImage', $js);
    }
}
