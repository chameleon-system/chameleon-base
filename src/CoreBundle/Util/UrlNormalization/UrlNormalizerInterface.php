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

/**
 * UrlNormalizerInterface defines a single normalizer which can be chained to perform complex modifications on URLs or
 * URL parts.
 */
interface UrlNormalizerInterface
{
    /**
     * Runs this normalizer and returns the normalized URL.
     *
     * @param string $url the URL to be normalized
     * @param string $spacer character that is used to replace certain special characters (depending on the implementation)
     *
     * @return string The normalized URL
     */
    public function normalizeUrl($url, $spacer);
}
