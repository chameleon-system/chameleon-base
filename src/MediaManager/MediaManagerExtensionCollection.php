<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\MediaManager;

use ChameleonSystem\MediaManager\Interfaces\MediaManagerExtensionInterface;

class MediaManagerExtensionCollection
{
    /**
     * @var MediaManagerExtensionInterface[]
     */
    private $extensions = [];

    /**
     * @return void
     */
    public function addExtension(MediaManagerExtensionInterface $mediaManagerExtension)
    {
        $this->extensions[] = $mediaManagerExtension;
    }

    /**
     * @return MediaManagerExtensionInterface[]
     */
    public function getExtensions()
    {
        return $this->extensions;
    }
}
