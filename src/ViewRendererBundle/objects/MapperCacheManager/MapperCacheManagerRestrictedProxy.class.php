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
class MapperCacheManagerRestrictedProxy implements IMapperCacheManagerRestricted
{
    /** @var IMapperCacheManager */
    private $oMapperCacheManager = null;
    private $bHasDisableCacheRequest = false;

    public function __construct(IMapperCacheManager $oMapperCacheManager)
    {
        $this->oMapperCacheManager = $oMapperCacheManager;
    }

    /**
     * @param int $iLifetimeInSeconds
     */
    public function setCacheLifetime($iLifetimeInSeconds)
    {
        $this->oMapperCacheManager->setCacheLifetime($iLifetimeInSeconds);
    }

    /**
     * @param string $sKey
     * @param mixed  $sData
     */
    public function addIdentificationToken($sKey, $sData = null)
    {
        $this->oMapperCacheManager->addIdentificationToken($sKey, $sData);
    }

    /**
     * if setCacheEnabled is called with false once, then caching can NOT be re-enabled by subsequent calls.
     *
     * @param bool $bCacheEnabled
     */
    public function setCacheEnabled($bCacheEnabled)
    {
        if (false === $bCacheEnabled) {
            $this->bHasDisableCacheRequest = true;
            $this->oMapperCacheManager->setCacheEnabled($bCacheEnabled);
        }
        if (false === $this->bHasDisableCacheRequest) {
            $this->oMapperCacheManager->setCacheEnabled($bCacheEnabled);
        }
    }
}
