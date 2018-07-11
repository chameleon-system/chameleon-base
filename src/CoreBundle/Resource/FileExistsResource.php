<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Resource;

use Symfony\Component\Config\Resource\FileResource;

class FileExistsResource extends FileResource
{
    public function isFresh($timestamp)
    {
        return file_exists($this->getResource());
    }
}
