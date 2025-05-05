<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ImageCrop\Interfaces;

use ChameleonSystem\ImageCrop\DataModel\ImageCropDataModel;
use ChameleonSystem\ImageCrop\DataModel\ImageDataModel;

/**
 * Crops images.
 */
interface CropImageServiceInterface
{
    /**
     * Get the cropped image for a crop definition.
     *
     * @return ImageDataModel|null
     */
    public function getCroppedImage(ImageCropDataModel $imageCrop);

    /**
     * Get the cropped image for an image and preset id.
     *
     * @param string $cmsMediaId
     * @param string $presetId
     * @param string $languageId
     * @param bool $fallbackOnNoCrop - create crop from original image based on preset height/width if there is no existing crop
     *
     * @return ImageDataModel|null
     */
    public function getCroppedImageForCmsMediaIdAndPresetId(
        $cmsMediaId,
        $presetId,
        $languageId,
        $fallbackOnNoCrop = true
    );

    /**
     * Get the cropped image for an image and crop id.
     *
     * @param string $cmsMediaId
     * @param string $cropId
     * @param string $languageId
     * @param int $targetWidth - 0 = not set
     * @param int $targetHeight - 0 = not set
     *
     * @return ImageDataModel|null
     */
    public function getCroppedImageForCmsMediaIdAndCropId(
        $cmsMediaId,
        $cropId,
        $languageId,
        $targetWidth = 0,
        $targetHeight = 0
    );
}
