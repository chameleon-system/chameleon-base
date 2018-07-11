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
 * @deprecated since 6.0.12 - it makes no sense to silently change/filter any characters in an email address. Invalid
 * characters should lead to an error message and might additionally be filtered by FILTER_DEFAULT to ensure that
 * no characters that are dangerous for the system remain.
 */
class TCMSUserInput_EMail extends TCMSUserInput_BaseText
{
    /**
     * {@inheritdoc}
     */
    protected function FilterItem($sValue)
    {
        $sValue = parent::FilterItem($sValue);
        $sValue = filter_var($sValue, FILTER_SANITIZE_EMAIL);

        return $sValue;
    }
}
