<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSUserInput_Decimal extends TCMSUserInput_BaseText
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
        $sValue = filter_var($sValue, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND | FILTER_FLAG_ALLOW_SCIENTIFIC);

        return $sValue;
    }
}
