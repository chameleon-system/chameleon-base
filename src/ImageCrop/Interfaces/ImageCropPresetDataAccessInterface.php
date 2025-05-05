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

use ChameleonSystem\ImageCrop\DataModel\ImageCropPresetDataModel;

/**
 * Accesses image crop presets.
 */
interface ImageCropPresetDataAccessInterface
{
    /**
     * Get a crop preset by id.
     *
     * @param string $id
     * @param string|null $languageId
     *
     * @return ImageCropPresetDataModel|null
     */
    public function getPresetById($id, $languageId = null);

    /**
     * Get a crop preset by its system name.
     *
     * @param string $systemName
     * @param string|null $languageId
     *
     * @return ImageCropPresetDataModel|null
     */
    public function getPresetBySystemName($systemName, $languageId = null);
}
