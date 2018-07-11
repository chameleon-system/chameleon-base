<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\CoreBundle\Util\MediaPathGenerator;

/**
 * Field that displays a generated media path directly from a given path part.
 */
class TCMSFieldMediaPath extends TCMSFieldSEOURLTitle
{
    /**
     * {@inheritdoc}
     */
    public function GetFilteredSEOURLTitle()
    {
        $inputFilterUtil = $this->getInputFilterUtil();
        $mediaId = $inputFilterUtil->getFilteredInput('id');
        $media = TdbCmsMedia::GetNewInstance($mediaId);
        $title = $inputFilterUtil->getFilteredInput('title', '');

        $filetype = $media->GetFieldCmsFiletype();
        $extension = (null === $filetype) ? '' : $filetype->fieldFileExtension;

        return $this->getMediaPathGenerator()->generateMediaPath($title, $extension, $mediaId, $media->sqlData['cmsident']);
    }

    /**
     * {@inheritdoc}
     */
    protected function getInputFieldAttributes()
    {
        $attributes = parent::getInputFieldAttributes();
        $attributes['readonly'] = 'readonly';

        return $attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function ConvertPostDataToSQL()
    {
        return $this->data; // We assume that data was already converted and avoid double-conversion by the parent method.
    }

    /**
     * @return InputFilterUtilInterface
     */
    private function getInputFilterUtil()
    {
        return ServiceLocator::get('chameleon_system_core.util.input_filter');
    }

    /**
     * @return MediaPathGenerator
     */
    private function getMediaPathGenerator()
    {
        return ServiceLocator::get('chameleon_system_core.util.media_path_generator');
    }
}
