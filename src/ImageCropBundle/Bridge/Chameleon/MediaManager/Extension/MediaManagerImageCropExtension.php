<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ImageCropBundle\Bridge\Chameleon\MediaManager\Extension;

use ChameleonSystem\MediaManager\Interfaces\MediaManagerExtensionInterface;

class MediaManagerImageCropExtension implements MediaManagerExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function registerDetailMappers()
    {
        return [
            'chameleon_system_image_crop.mapper.media_manager_image_crop_access_rights',
            'chameleon_system_image_crop.mapper.media_manager_image_crop_editor_url',
            'chameleon_system_image_crop.mapper.media_manager_media_item_crops',
            'chameleon_system_image_crop.mapper.usages_add_crops_mapper',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function registerAdditionalTemplatesForDetailViewButtons()
    {
        return ['imageCrop/mediaManager/detailButtons.html.twig'];
    }

    /**
     * {@inheritdoc}
     */
    public function registerAdditionalTemplatesForDetailView()
    {
        return ['imageCrop/mediaManager/detailCrops.html.twig'];
    }
}
