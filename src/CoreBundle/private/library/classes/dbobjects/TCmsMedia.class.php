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
use ChameleonSystem\CoreBundle\Util\MediaPathGenerator;

class TCmsMedia extends TCmsMediaAutoParent
{
    public function GetImageNameAsSeoName()
    {
        $oType = $this->GetFieldCmsFiletype();
        $extension = (null === $oType) ? '' : $oType->fieldFileExtension;

        return $this->getMediaPathGenerator()->generateMediaPath($this->fieldCustomFilename, $extension, $this->id, $this->sqlData['cmsident']);
    }

    /**
     * @return MediaPathGenerator
     */
    private function getMediaPathGenerator()
    {
        return ServiceLocator::get('chameleon_system_core.util.media_path_generator');
    }
}
