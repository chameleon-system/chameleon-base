<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ImageCropBundle\Bridge\Chameleon\Service;

use ChameleonSystem\ImageCrop\DataModel\CmsMediaDataModel;
use ChameleonSystem\ImageCrop\DataModel\ImageCropDataModel;
use ChameleonSystem\ImageCrop\DataModel\ImageCropPresetDataModel;
use ChameleonSystem\ImageCrop\DataModel\ImageDataModel;
use ChameleonSystem\ImageCrop\Interfaces\CmsMediaDataAccessInterface;
use ChameleonSystem\ImageCrop\Interfaces\CropImageServiceInterface;
use ChameleonSystem\ImageCrop\Interfaces\ImageCropDataAccessInterface;
use ChameleonSystem\ImageCrop\Interfaces\ImageCropPresetDataAccessInterface;

class CropImageService implements CropImageServiceInterface
{
    /**
     * @var CmsMediaDataAccessInterface
     */
    private $cmsMediaDataAccess;

    /**
     * @var ImageCropPresetDataAccessInterface
     */
    private $imageCropPresetDataAccess;

    /**
     * @var ImageCropDataAccessInterface
     */
    private $imageCropDataAccess;

    public function __construct(
        CmsMediaDataAccessInterface $cmsMediaDataAccess,
        ImageCropPresetDataAccessInterface $imageCropPresetDataAccess,
        ImageCropDataAccessInterface $imageCropDataAccess
    ) {
        $this->cmsMediaDataAccess = $cmsMediaDataAccess;
        $this->imageCropPresetDataAccess = $imageCropPresetDataAccess;
        $this->imageCropDataAccess = $imageCropDataAccess;
    }

    /**
     * {@inheritdoc}
     */
    public function getCroppedImageForCmsMediaIdAndPresetId(
        $cmsMediaId,
        $presetId,
        $languageId,
        $fallbackOnNoCrop = true,
        $maxTargetWidth = 0
    ) {
        $preset = $this->imageCropPresetDataAccess->getPresetById($presetId);
        if (null === $preset) {
            return null;
        }

        $cmsMedia = $this->cmsMediaDataAccess->getCmsMedia($cmsMediaId, $languageId);
        if (null === $cmsMedia) {
            $cmsMedia = new CmsMediaDataModel(
                '1',
                PATH_USER_CMS_PUBLIC.'../'.CHAMELEON_404_IMAGE_PATH_BIG,
                '',
                CHAMELEON_404_IMAGE_PATH_BIG
            );
        }

        $crop = $this->imageCropDataAccess->getImageCrop($cmsMedia, $preset);
        if (null === $crop) {
            if (false === $fallbackOnNoCrop) {
                return null;
            }

            return $this->createCropAndCheckMaxTargetWidth($cmsMediaId, $preset, $maxTargetWidth);
        }

        // Image crop exists in Media manager!
        // if $maxTargetWidth isn't used (=0) or
        // $crop fits or is even smaller, then get this crop and return
        if ($maxTargetWidth === 0 || $preset->getWidth() <= $maxTargetWidth || $crop->getImageCropPreset()->getWidth() <= $maxTargetWidth) {
            return $this->getCroppedImage($crop);
        }

        return $this->convertCropToImageAndDecreaseToMaxTargetWidth($crop, $maxTargetWidth);
    }

    private function createCropAndCheckMaxTargetWidth(string $cmsMediaId, ImageCropPresetDataModel $preset, int $maxTargetWidth = 0): ImageDataModel
    {
        $cmsImage = new \TCMSImage($cmsMediaId);
        $croppedImage = $cmsImage->GetForcedSizeThumbnail($preset->getWidth(), $preset->getHeight());

        if ($maxTargetWidth > 0 && $croppedImage->aData['width'] > $maxTargetWidth) {
            return $this->decreaseThumbnailToTargetWidth($croppedImage, $maxTargetWidth);
        }

        return new ImageDataModel($croppedImage->GetFullURL());
    }

    private function convertCropToImageAndDecreaseToMaxTargetWidth(ImageCropDataModel $crop, int $maxTargetWidth = 0): ImageDataModel
    {
        // Create a TCMSImage for using decreaseThumbnail with datas of $crop
        $image = new \TCMSImage($crop->getCmsMedia()->getId());

        $croppedImage = $image->getCroppedImage(
            $crop->getImageCropPreset()->getWidth(),
            $crop->getImageCropPreset()->getHeight(),
            $crop->getWidth(),
            $crop->getHeight(),
            $crop->getPosX(),
            $crop->getPosY()
        );

        return $this->decreaseThumbnailToTargetWidth($croppedImage, $maxTargetWidth);
    }

    private function decreaseThumbnailToTargetWidth(\TCMSImage $croppedImage, string $maxTargetWidth): ImageDataModel
    {
        $croppedImage->_isThumbnail = true;
        $croppedImage->aData['thumbOriginalUrl'] = $croppedImage->GetFullLocalPath();

        $cropSmall = $croppedImage->decreaseThumbnail($maxTargetWidth);

        if (null === $cropSmall) {
            return new ImageDataModel($croppedImage->GetFullURL());
        }

        return new ImageDataModel($cropSmall->GetFullURL());
    }

    /**
     * {@inheritdoc}
     */
    public function getCroppedImage(ImageCropDataModel $imageCrop)
    {
        $image = new \TCMSImage($imageCrop->getCmsMedia()->getId());

        $targetWidth = $imageCrop->getWidth();
        $targetHeight = $imageCrop->getHeight();
        $preset = $imageCrop->getImageCropPreset();
        if (null !== $preset) {
            $targetWidth = $preset->getWidth();
            $targetHeight = $preset->getHeight();
        }

        if (null !== $imageCrop->getTargetWidth()) {
            $targetWidth = $imageCrop->getTargetWidth();
        }
        if (null !== $imageCrop->getTargetHeight()) {
            $targetHeight = $imageCrop->getTargetHeight();
        }

        $croppedImage = $image->getCroppedImage(
            $targetWidth,
            $targetHeight,
            $imageCrop->getWidth(),
            $imageCrop->getHeight(),
            $imageCrop->getPosX(),
            $imageCrop->getPosY()
        );

        if (null === $croppedImage) {
            return null;
        }

        return new ImageDataModel($croppedImage->GetFullURL());
    }

    /**
     * {@inheritdoc}
     */
    public function getCroppedImageForCmsMediaIdAndCropId(
        $cmsMediaId,
        $cropId,
        $languageId,
        $targetWidth = 0,
        $targetHeight = 0
    ) {
        $crop = $this->imageCropDataAccess->getImageCropById($cropId, $languageId);
        if (null === $crop) {
            return null;
        }

        if (0 !== $targetWidth) {
            $ratio = $targetWidth / $crop->getWidth();
            $height = $crop->getHeight() * $ratio;
            $crop->setTargetHeight((int) $height);
            $crop->setTargetWidth($targetWidth);
        }

        if (0 !== $targetHeight) {
            $ratio = $targetHeight / $crop->getHeight();
            $width = $crop->getWidth() * $ratio;
            $crop->setTargetHeight($targetHeight);
            $crop->setTargetWidth((int) $width);
        }

        return $this->getCroppedImage($crop);
    }
}
