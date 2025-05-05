<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Util;

interface MediaPathGeneratorInterface
{
    /**
     * Returns the path for a media file. The resulting path has the following properties:
     * - It is relative to the base media directory (normally defined by the constant PATH_MEDIA_LIBRARY).
     * - It has a slash suffix but NO slash prefix.
     * - It is generated from passed image information, not from the real image path.
     * - It is file system safe (media can be saved under the resulting path without further escaping, although the path
     *   may contain subdirectories which may not yet exist).
     * - It is URL safe (media can be downloaded from the resulting path without further escaping).
     *
     * @param string $rawFileName
     * @param string $rawFileExtension
     * @param string $imageId
     * @param int $imageCmsIdent
     *
     * @return string
     */
    public function generateMediaPath($rawFileName, $rawFileExtension, $imageId, $imageCmsIdent);
}
