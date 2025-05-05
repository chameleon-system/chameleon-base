<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Util\UrlNormalization;

class UrlNormalizationUtil
{
    /**
     * @var UrlNormalizerInterface[]
     */
    private $normalizerList = [];

    /**
     * @return void
     */
    public function addNormalizer(UrlNormalizerInterface $urlNormalizer)
    {
        $this->normalizerList[] = $urlNormalizer;
    }

    /**
     * Replaces special characters in URLs or URL parts by running a list of single-purpose normalizers.
     *
     * @param string $url the URL to normalize
     * @param string $spacer Character that is used to replace certain special characters (depending on the normalizer implementation)
     *
     * @return string The normalized URL
     */
    public function normalizeUrl($url, $spacer = '-')
    {
        foreach ($this->normalizerList as $normalizer) {
            $url = $normalizer->normalizeUrl($url, $spacer);
        }

        return $url;
    }
}
