<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgCmsCaptcha_Math extends TdbPkgCmsCaptcha
{
    /**
     * @param string $sIdentifier
     * @param int $iCharacters
     *
     * @return string
     */
    protected function GenerateCode($sIdentifier, $iCharacters)
    {
        /** @var array<string, string> $aCodeCache */
        static $aCodeCache = []; // generate a code for an identifier only once within one session call
        if (!array_key_exists($sIdentifier, $aCodeCache)) {
            $a = rand(1, 20);
            $b = rand(1, 20);

            $code = $a.' + '.$b;
            $aCodeCache[$sIdentifier] = $code;
            TdbPkgCmsCaptcha::SaveInSession($sIdentifier, $a + $b);
        }

        return $aCodeCache[$sIdentifier];
    }
}
