<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSUserInput_SafeText extends TCMSUserInput_SafeTextBlock
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
        $sValue = parent::FilterItem($sValue);

        // also remove data not likely part of the usual text input...
        $aInvalid = ['#', ';', '=', "\n", "\r", "\t"];
        $sValue = str_replace($aInvalid, '', $sValue);

        return $sValue;
    }
}
