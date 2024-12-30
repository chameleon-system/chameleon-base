<?php

namespace ChameleonSystem\ImageEditorBundle\Bridge\Chameleon\MediaManager\Extension;

use ChameleonSystem\MediaManager\Interfaces\MediaManagerExtensionInterface;

class MediaManagerImageEditorExtension implements MediaManagerExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function registerDetailMappers()
    {
        return [
            'chameleon_system_image_editor.bridge_chameleon_media_manager_mapper.image_editor_mapper',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function registerAdditionalTemplatesForDetailViewButtons()
    {
        return ['imageEditor/mediaManager/detailButtons.html.twig'];
    }

    /**
     * {@inheritdoc}
     */
    public function registerAdditionalTemplatesForDetailView()
    {
        return [];
    }
}
