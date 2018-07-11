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
interface IMapperCacheManager extends IMapperCacheManagerRestricted
{
    /**
     * The key, that is used in the cached array to identify the rendered content.
     *
     * @abstract
     *
     * @return string
     */
    public function getContentKey();

    /**
     * @abstract
     *
     * @return mixed
     */
    public function getCachedContent();

    /**
     * @abstract
     *
     * @param mixed               $sContentToCache
     * @param IMapperCacheTrigger $oTrigger
     */
    public function setCachedContent($sContentToCache, IMapperCacheTrigger $oTrigger = null);

    /**
     * @abstract
     *
     * @return bool
     */
    public function hasCachedContent();

    /**
     * @abstract
     *
     * @return array
     */
    public function getIdentificationTokens();

    /**
     * @abstract
     *
     * @return bool
     */
    public function getCacheEnabled();

    public function getCacheKey();

    public function setTokenPrefix($sPrefix);
}
