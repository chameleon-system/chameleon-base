<?php

namespace ChameleonSystem\ImageEditorBundle\Bridge\Chameleon\MediaManager\Mapper;

use ChameleonSystem\ImageEditorBundle\Interface\ImageEditorUrlServiceInterface;
use ChameleonSystem\MediaManager\DataModel\MediaItemDataModel;

class ImageEditorMapper extends \AbstractViewMapper
{
    public function __construct(
        private readonly ImageEditorUrlServiceInterface $editorUrlService
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

        $url = $this->editorUrlService->getImageEditorUrl($imageId, $mediaItem->getWidth(), $mediaItem->getHeight());
        $oVisitor->SetMappedValue('createEditorUrl', $url);
    }
}
