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

/**
 * Accesses images in the media database.
 */
interface CmsMediaDataAccessInterface
{
    /**
     * Get an image from the media database.
     *
     * @param string $id
     * @param string $languageId
     *
     * @return CmsMediaDataModel|null
     */
    public function getCmsMedia($id, $languageId);
}
