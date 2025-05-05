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

class UrlNormalizerRomanian implements UrlNormalizerInterface
{
    /**
     * @var array<string, string>
     */
    private static $romanianCharNormalization = [
        'Ă' => 'A',
        'ă' => 'a',
        'Ș' => 'S',
        'ș' => 's',
        'Ş' => 'S',
        'ş' => 's',
        'Ț' => 'T',
        'ț' => 't',
        'Ţ' => 'T',
        'ţ' => 't',
    ];

    /**
     * {@inheritdoc}
     */
    public function normalizeUrl($url, $spacer)
    {
        return strtr($url, self::$romanianCharNormalization);
    }
}
