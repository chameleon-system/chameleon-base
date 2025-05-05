<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgShopServiceType_Giftcard extends TdbPkgShopServiceType
{
    /**
     * filter the user data. make sure you overwrite this method for each type to allow the parameter that you want to
     * let through.
     */
    public function FilterUserInput(array &$aUserInput): void
    {
        $aPermittedData = ['cardtext'];
        $aKeys = array_keys($aUserInput);
        foreach ($aKeys as $sKey) {
            if (!in_array($sKey, $aPermittedData)) {
                unset($aUserInput[$sKey]);
            }
        }
    }
}
