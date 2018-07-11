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

class UrlNormalizerBasicStart implements UrlNormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function normalizeUrl($url, $spacer)
    {
        $url = urldecode($url);
        $url = trim($url);

        return html_entity_decode($url, ENT_COMPAT, 'UTF-8');
    }
}
