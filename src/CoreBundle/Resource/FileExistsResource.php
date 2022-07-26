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

use Symfony\Component\Config\Resource\FileExistenceResource;
use Symfony\Component\Config\Resource\FileResource;

/**
 * @psalm-suppress InvalidExtendClass - `FileResource` is marked @final
 * @psalm-suppress MethodSignatureMismatch - `FileResource` is marked @final
 *
 * Can potentially be completely replaced with {@see FileExistenceResource}
 */
class FileExistsResource extends FileResource
{
    public function isFresh($timestamp)
    {
        return file_exists($this->getResource());
    }
}
