<?php

namespace ChameleonSystem\ImageEditorBundle\Bridge\Chameleon\MediaManager\Mapper;

use ChameleonSystem\CoreBundle\Util\UrlUtil;
use ChameleonSystem\ImageCropBundle\Bridge\Chameleon\BackendModule\ImageCropEditorModule;
use ChameleonSystem\ImageEditorBundle\Bridge\Chameleon\BackendModule\ImageEditorModule;
use ChameleonSystem\MediaManager\DataModel\MediaItemDataModel;

class ImageEditorMapper extends \AbstractViewMapper
{
    public function __construct(
        private readonly UrlUtil $urlUtil
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function GetRequirements(\IMapperRequirementsRestricted $oRequirements): void
    {
        $oRequirements->NeedsSourceObject('mediaItem', MediaItemDataModel::class);
    }

    /**
     * {@inheritdoc}
     */
    public function Accept(
        \IMapperVisitorRestricted $oVisitor,
        $bCachingEnabled,
        \IMapperCacheTriggerRestricted $oCacheTriggerManager
    ): void {
        /**
         * @var $mediaItem MediaItemDataModel
         */
        $mediaItem = $oVisitor->GetSourceObject('mediaItem');

        $imageId = $mediaItem->getId();

        if ('' === $imageId) {
            return;
        }

        $url = $this->getImageEditorUrl($imageId);
        $oVisitor->SetMappedValue('createEditorUrl', $url);
    }

    private function getImageEditorUrl(string $mediaItemId): string
    {
        $parameters = [
            'pagedef' => ImageEditorModule::PAGEDEF_NAME,
            '_pagedefType' => ImageEditorModule::PAGEDEF_TYPE,
            ImageCropEditorModule::URL_PARAM_IMAGE_ID => $mediaItemId,
        ];

        return URL_CMS_CONTROLLER.$this->urlUtil->getArrayAsUrl($parameters, '?', '&');
    }
}
