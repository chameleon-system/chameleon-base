<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSUserInput_InternalURL extends TCMSUserInput_URL
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
        $redirect = ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.redirect');
        if (true === $redirect->isInternalURL($sValue)) {
            return $sValue;
        }

        return false;
    }
}
