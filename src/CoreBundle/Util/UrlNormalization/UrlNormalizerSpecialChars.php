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

class UrlNormalizerSpecialChars implements UrlNormalizerInterface
{
    /**
     * @var array<string, string>
     */
    private static $specialCharNormalization = array(
        '®' => '',
        '™' => '',
        '€' => ' EUR',
        '$' => ' USD',
        '§' => '',
        '₵' => '',
        '¢' => '',
        '₡' => '',
        '₫' => '',
        'ƒ' => '',
        '₲' => '',
        '₭' => '',
        '£' => '',
        '₤' => '',
        '₥' => '',
        '₦' => '',
        '₱' => '',
        '₨' => '',
        '₮' => '',
        '₩' => '',
        '¥' => 'YEN',
        '₴' => '',
        '₪' => '',
        '!' => '',
        '?' => '',
        ':' => '',
        ';' => '',
        '&' => '',
        '‘' => '',
        '’' => '',
        '‚' => '',
        '“' => '',
        '”' => '',
        '„' => '',
        '†' => '',
        '‡' => '',
        '‰' => '',
        '‹' => '',
        '›' => '',
        '♠' => '',
        '♣' => '',
        '♥' => '',
        '♦' => '',
        '‾' => '',
        '←' => '',
        '↑' => '',
        '→' => '',
        '↓' => '',
        '"' => '',
        '%' => '',
        "'" => '',
        '(' => '',
        ')' => '',
        '*' => '',
        '+' => '',
        '<' => '',
        '=' => '',
        '>' => '',
        '@' => '',
        '[' => '',
        ']' => '',
        '{' => '',
        '}' => '',
        '~' => '',
        '–' => '',
        '—' => '',
        '¡' => '',
        '¤' => '',
        '¦' => '',
        '¨' => '',
        'ª' => '',
        '©' => '',
        '«' => '',
        '¯' => '',
        '°' => '',
        '±' => '',
        '¹' => '',
        '²' => '',
        '³' => '',
        '`' => '',
        '´' => '',
        'µ' => '',
        '¶' => '',
        '·' => '',
        '¸' => '',
        'º' => '',
        '»' => '',
        '¼' => '',
        '¾' => '',
        '¿' => '',
        'Æ' => 'AE',
        'Ð' => 'D',
        '×' => '',
        'Ø' => '',
        'Þ' => '',
        '|' => '',
        '\\' => '',
    );

    /**
     * The third index in the search array is a special character. It looks like a space but it is a special space
     * (from mac). This special character has the effect that the following text won't wrap until the next normal
     * space or other character (that allows wrapping) appears.
     *
     * @var string[]
     */
    private static $specialCharToSpacerNormalization = array(
        '/',
        ' ',
        ' ',
        ',',
        '.',
        '&',
    );

    /**
     * {@inheritdoc}
     */
    public function normalizeUrl($url, $spacer)
    {
        $url = strtr($url, self::$specialCharNormalization);

        return str_replace(self::$specialCharToSpacerNormalization, $spacer, $url);
    }
}
