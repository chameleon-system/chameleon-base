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
 * @deprecated since 6.2.0 - no longer used.
 */
interface IPkgCoreDbClassFactory
{
    /**
     * @param string|array|null $sData
     * @param string|null       $sLanguage
     *
     * @return TCMSRecord
     */
    public static function GetNewInstance($sData = null, $sLanguage = null);
}
