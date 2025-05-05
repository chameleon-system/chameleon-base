<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSUserInput_URL extends TCMSUserInput_BaseText
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
        if (function_exists('filter_var')) {
            $sValue = filter_var($sValue, FILTER_SANITIZE_URL);
        }

        $aEscaped = ["'", '&#039;', '&#39;', '"', '&quot;', '&#034;', '&#34;'];
        $sValue = str_replace($aEscaped, '', $sValue);

        return $sValue;
    }
}
