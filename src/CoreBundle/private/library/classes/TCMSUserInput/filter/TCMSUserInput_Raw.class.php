<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * base class for filter class.
 */
class TCMSUserInput_Raw extends TCMSUserInputFilter_BaseClass
{
    /**
     * filter a single item.
     *
     * @param string $sValue
     *
     * @return string
     */
    protected function FilterItem($sValue)
    {
        // protect content below 32 bit that we want to keep
        if (function_exists('filter_var')) {
            $aProtect = ["\n" => '[{_SLASH-N_}]', "\r" => '[{_SLASH-R_}]', "\t" => '[{_SLASH-T_}]'];
            $aProtectProtect = ['[{_SLASH-N_}]' => '[{_SLASH-N__}]', '[{_SLASH-R_}]' => '[{_SLASH-R__}]', '[{_SLASH-T_}]' => '[{_SLASH-T__}]'];

            // prevent others from abusing the method...
            $sValue = str_replace(array_keys($aProtectProtect), array_values($aProtectProtect), $sValue);

            $sValue = str_replace(array_keys($aProtect), array_values($aProtect), $sValue);
            $sValue = filter_var($sValue, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW); // remove chars below 32 bit. they are never save - except for TAB, LF, CR
            $sValue = str_replace(array_values($aProtect), array_keys($aProtect), $sValue);
            $sValue = str_replace(array_values($aProtectProtect), array_keys($aProtectProtect), $sValue);
        } else {
            $sValue = str_replace(chr(0), '', $sValue); // remove at least null byte
        }

        return $sValue;
    }
}
