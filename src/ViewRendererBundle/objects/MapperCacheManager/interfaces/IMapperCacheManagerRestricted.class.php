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
interface IMapperCacheManagerRestricted
{
    /**
     * @abstract
     *
     * @param int $iLifetimeInSeconds
     */
    public function setCacheLifetime($iLifetimeInSeconds);

    /**
     * @abstract
     *
     * @param string $sKey
     * @param mixed  $sData
     */
    public function addIdentificationToken($sKey, $sData = null);

    /**
     * @abstract
     *
     * @param bool $bCacheEnabled
     */
    public function setCacheEnabled($bCacheEnabled);
}
