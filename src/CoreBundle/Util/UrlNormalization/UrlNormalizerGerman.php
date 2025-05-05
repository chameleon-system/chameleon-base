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

class UrlNormalizerGerman implements UrlNormalizerInterface
{
    /**
     * @var array<string, string>
     */
    private static $germanCharNormalization = [
        'ä' => 'ae',
        'ö' => 'oe',
        'ü' => 'ue',
        'ß' => 'ss',
        'Ä' => 'Ae',
        'Ö' => 'Oe',
        'Ü' => 'Ue',
    ];

    /**
     * {@inheritdoc}
     */
    public function normalizeUrl($url, $spacer)
    {
        return strtr($url, self::$germanCharNormalization);
    }
}
