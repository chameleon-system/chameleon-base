<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSUserInput_BaseText extends TCMSUserInput_Raw
{
    private static $protectedCharacters = [
        "\n",
        "\r",
        "\t",
    ];

    private static $placeholders = [
        '[{_SLASH-N_}]',
        '[{_SLASH-R_}]',
        '[{_SLASH-T_}]',
    ];

    private static $doubledPlaceholders = [
        '[{_SLASH-N__}]',
        '[{_SLASH-R__}]',
        '[{_SLASH-T__}]',
    ];

    /**
     * filter a single item.
     *
     * @param string $value
     *
     * @return string
     */
    protected function FilterItem($value)
    {
        $value = parent::FilterItem($value);

        // Prevent others from abusing the protectionmethod...
        $value = str_replace(self::$placeholders, self::$doubledPlaceholders, $value);
        // Protect characters below 32 that we want to keep
        $value = str_replace(self::$protectedCharacters, self::$placeholders, $value);
        // Remove characters below 32. they are never safe - except for TAB, LF, CR
        $value = filter_var($value, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW);
        // Restore protected characters
        $value = str_replace(self::$placeholders, self::$protectedCharacters, $value);
        $value = str_replace(self::$doubledPlaceholders, self::$placeholders, $value);

        return $value;
    }
}
