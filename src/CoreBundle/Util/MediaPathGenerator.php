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

use ChameleonSystem\CoreBundle\Util\UrlNormalization\UrlNormalizationUtil;

class MediaPathGenerator implements MediaPathGeneratorInterface
{
    /**
     * @var UrlNormalizationUtil
     */
    private $urlNormalizationUtil;

    public function __construct(UrlNormalizationUtil $urlNormalizationUtil)
    {
        $this->urlNormalizationUtil = $urlNormalizationUtil;
    }

    /**
     * {@inheritdoc}
     */
    public function generateMediaPath($rawFileName, $rawFileExtension, $imageId, $imageCmsIdent)
    {
        $path = \trim($rawFileName).'_id'.$imageCmsIdent;
        $path = $this->urlNormalizationUtil->normalizeUrl($path);
        $path = \mb_strtolower($path);
        $path = \TTools::sanitizeFilename($path, $rawFileExtension);
        if (36 === \strlen($imageId)) {
            $id = $imageId;
        } else {
            $id = \md5($imageId);
        }
        $prefix = \sprintf('%s/%s%s/', $id[0], $id[1], $id[2]);
        $path = $prefix.$path;

        return $path;
    }
}
