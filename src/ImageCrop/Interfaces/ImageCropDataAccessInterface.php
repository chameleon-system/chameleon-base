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

use ChameleonSystem\ImageCrop\DataModel\CmsMediaDataModel;
use ChameleonSystem\ImageCrop\DataModel\ImageCropDataModel;
use ChameleonSystem\ImageCrop\DataModel\ImageCropPresetDataModel;
use ChameleonSystem\ImageCrop\Exception\ImageCropDataAccessException;

/**
 * Accesses image crops.
 */
interface ImageCropDataAccessInterface
{
    /**
     * Insert a crop definition.
     *
     * @return string - the inserted id
     *
     * @throws ImageCropDataAccessException
     */
    public function insertImageCrop(ImageCropDataModel $imageCrop);

    /**
     * Update a crop definition.
     *
     * @return void
     *
     * @throws ImageCropDataAccessException
     */
    public function updateImageCrop(ImageCropDataModel $imageCrop);

    /**
     * Get the crop definition for an image and a preset.
     *
     * @return ImageCropDataModel|null
     */
    public function getImageCrop(CmsMediaDataModel $cmsMedia, ImageCropPresetDataModel $preset);

    /**
     * Get a crop definition by id.
     *
     * @param string $cropId
     * @param string $languageId
     *
     * @return ImageCropDataModel|null
     */
    public function getImageCropById($cropId, $languageId);

    /**
     * Get all crop definitions for an image.
     *
     * @return ImageCropDataModel[]
     *
     * @throws ImageCropDataAccessException
     */
    public function getExistingCrops(CmsMediaDataModel $cmsMedia);

    /**
     * @throws ImageCropDataAccessException
     */
    public function deleteCrop(string $cropId): void;
}
