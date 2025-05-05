<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ImageCropBundle\Bridge\Chameleon\MediaManager\Mapper;

use ChameleonSystem\ImageCrop\Exception\ImageCropDataAccessException;
use ChameleonSystem\ImageCrop\Interfaces\CmsMediaDataAccessInterface;
use ChameleonSystem\ImageCrop\Interfaces\ImageCropDataAccessInterface;
use ChameleonSystem\MediaManager\DataModel\MediaItemDataModel;

class MediaManagerMediaItemCropsMapper extends \AbstractViewMapper
{
    /**
     * @var ImageCropDataAccessInterface
     */
    private $imageCropDataAccess;

    /**
     * @var CmsMediaDataAccessInterface
     */
    private $cmsMediaDataAccess;

    public function __construct(
        ImageCropDataAccessInterface $imageCropDataAccess,
        CmsMediaDataAccessInterface $cmsMediaDataAccess
    ) {
        $this->imageCropDataAccess = $imageCropDataAccess;
        $this->cmsMediaDataAccess = $cmsMediaDataAccess;
    }

    /**
     * {@inheritdoc}
     */
    public function GetRequirements(\IMapperRequirementsRestricted $oRequirements): void
    {
        $oRequirements->NeedsSourceObject('mediaItem', MediaItemDataModel::class);
        $oRequirements->NeedsSourceObject('language', \TdbCmsLanguage::class);
    }

    /**
     * {@inheritDoc}
     */
    public function Accept(
        \IMapperVisitorRestricted $oVisitor,
        $bCachingEnabled,
        \IMapperCacheTriggerRestricted $oCacheTriggerManager
    ): void {
        /**
         * @var MediaItemDataModel $mediaItem
         * @var \TdbCmsLanguage $language
         */
        $mediaItem = $oVisitor->GetSourceObject('mediaItem');
        $language = $oVisitor->GetSourceObject('language');
        try {
            $cmsMedia = $this->cmsMediaDataAccess->getCmsMedia($mediaItem->getId(), $language->id);
            if (null === $cmsMedia) {
                throw new \MapperException(
                    sprintf(
                        'Trying to get usages of crops: CMS media object for media item with ID %s could not be found.',
                        $mediaItem->getId()
                    )
                );
            }
            $crops = $this->imageCropDataAccess->getExistingCrops($cmsMedia);
            $oVisitor->SetMappedValue('crops', $crops);
        } catch (ImageCropDataAccessException $e) {
            throw new \MapperException(
                sprintf('Error getting crops for media item with ID %s: %s', $mediaItem->getId(), $e->getMessage()),
                0,
                $e
            );
        }
    }
}
