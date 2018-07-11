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

class UrlNormalizerNonLatin implements UrlNormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function normalizeUrl($url, $spacer)
    {
        $foundNonLatinChars = false;
        if (preg_match('/[^a-zA-Z0-9\_-]/i', $url)) {
            $foundNonLatinChars = true;
        }

        $foundLatinChars = false;
        if (preg_match('/[a-zA-Z0-9]/i', $url)) {
            $foundLatinChars = true;
        }

        if (!$foundNonLatinChars && $foundLatinChars) {
            $url = preg_replace('/[^a-zA-Z0-9_-]/i', '', $url);
        } else {
            // encode URL because we have non-URL-valid chars like Chinese, Cyrillic etc.
            $url = urlencode($url);
        }

        return $url;
    }
}
