<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace esono\pkgCmsCache;

interface CacheInterface
{
    /**
     * allows you to disable caching during runtime.
     *
     * @return void
     */
    public function disable();

    /**
     * enable caching during runtime.
     *
     * @return void
     */
    public function enable();

    /**
     * @return bool
     */
    public function isActive();

    /**
     * return the contents for the key identified by $key
     * returns null if the key is not found.
     *
     * @param string $key - key generated with GetKey
     *
     * @return mixed|null - returns the cache object or null if not found
     */
    public function get($key);

    /**
     * adds or updates a cache object.
     *
     * @param string $key - the cache key
     * @param mixed $content - content to be stored
     * @param array $trigger - cache trigger array(array('table'=>'','id'=>''),array('table'=>'','id'=>''),...);
     * @param int $iMaxLiveInSeconds - max age in seconds before the cache content expires - default = 30 days
     *
     * @return void
     */
    public function set($key, $content, $trigger, $iMaxLiveInSeconds = null);

    /**
     * removes a cache object by key.
     *
     * @param string $key
     *
     * @return bool
     */
    public function delete($key);

    /**
     * Clears the whole cache. Operation is permitted even if caching is disabled.
     *
     * @return void
     */
    public function clearAll();

    /**
     * removes all cached objects based on table and optional record id.
     *
     * @param string $table
     * @param int|string $id
     *
     * @return void
     */
    public function callTrigger($table, $id = null);

    /**
     * returns a cache key for given parameter array.
     *
     * @param array $aParameters
     * @param bool $addStateKey
     *
     * @return string
     *
     * @throws \InvalidArgumentException if the key cannot be created with the given parameters
     */
    public function getKey($aParameters, $addStateKey = true);
}
