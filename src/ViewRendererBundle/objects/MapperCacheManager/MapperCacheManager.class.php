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
class MapperCacheManager implements IMapperCacheManager
{
    private $aIdentificationTokens = array();
    private $iLifetimeInSeconds = 0;
    private $bCacheEnabled = true;
    /** @var ICacheManager */
    private $oCacheManager = null;

    private $sLastCacheKeyFetched = null;
    private $sCachedContent = false;
    private $bHasCacheContent = false;

    private $sTokenPrefix = '';

    public function __construct(ICacheManager $oCacheManager)
    {
        $this->oCacheManager = $oCacheManager;
    }

    /**
     * @return mixed
     */
    public function getCachedContent()
    {
        $oContent = $this->getCachedContentHelper();
        if (false === $oContent) {
            throw new MapperCacheManagerExceptionContentNotFound('unable to find cache entry for key '.$this->getCacheKey());
        }

        return $oContent;
    }

    private function getCachedContentHelper()
    {
        if (false === $this->bCacheEnabled) {
            return false;
        }
        $sKey = $this->getCacheKey();
        if ($sKey === $this->sLastCacheKeyFetched) {
            return $this->sCachedContent;
        }

        $this->sCachedContent = $this->oCacheManager->GetContents($sKey);
        $this->sLastCacheKeyFetched = $sKey;
        if (false === $this->sCachedContent) {
            $this->bHasCacheContent = false;
        } else {
            $this->bHasCacheContent = true;
        }

        return $this->sCachedContent;
    }

    /**
     * @param mixed               $sContentToCache
     * @param IMapperCacheTrigger $oTrigger
     */
    public function setCachedContent($sContentToCache, IMapperCacheTrigger $oTrigger = null)
    {
    }

    public function getCacheKey()
    {
        return $this->oCacheManager->GetKey($this->aIdentificationTokens);
    }

    /**
     * pass 0 if you want the item to live forever.
     *
     * @param int $iLifetimeInSeconds
     */
    public function setCacheLifetime($iLifetimeInSeconds)
    {
        $this->iLifetimeInSeconds = $iLifetimeInSeconds;
    }

    /**
     * @param string $sKey
     * @param mixed  $sData
     */
    public function addIdentificationToken($sKey, $sData = null)
    {
        $this->aIdentificationTokens[$this->sTokenPrefix.$sKey] = $sData;
    }

    /**
     * @param bool $bCacheEnabled
     */
    public function setCacheEnabled($bCacheEnabled)
    {
        $this->bCacheEnabled = $bCacheEnabled;
    }

    /**
     * @return bool
     */
    public function hasCachedContent()
    {
        if (false === $this->getCacheEnabled()) {
            return false;
        }
        $this->getCachedContentHelper();

        return $this->bHasCacheContent;
    }

    /**
     * @return bool
     */
    public function getCacheEnabled()
    {
        return $this->bCacheEnabled;
    }

    /**
     * @return array
     */
    public function getIdentificationTokens()
    {
        return $this->aIdentificationTokens;
    }

    public function setTokenPrefix($sPrefix)
    {
        $this->sTokenPrefix = $sPrefix;
    }

    /**
     * @return string
     */
    public function getContentKey()
    {
        return 'content';
    }
}
