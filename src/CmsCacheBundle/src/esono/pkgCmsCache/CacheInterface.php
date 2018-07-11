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

use Symfony\Component\HttpFoundation\Request;

interface CacheInterface
{
    /**
     * allows you to disable caching during runtime.
     */
    public function disable();

    /**
     * enable caching during runtime.
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
     * @return mixed - returns the cache object or null if not found
     */
    public function get($key);

    /**
     * adds or updates a cache object.
     *
     * @param string $key               - the cache key
     * @param object $content           - object to be stored
     * @param array  $trigger           - cache trigger array(array('table'=>'','id'=>''),array('table'=>'','id'=>''),...);
     * @param int    $iMaxLiveInSeconds - max age in seconds before the cache content expires - default = 30 days
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
     */
    public function clearAll();

    /**
     * removes all cached objects based on table and optional record id.
     *
     * @param string     $table
     * @param int|string $id
     */
    public function callTrigger($table, $id = null);

    /**
     * returns a cache key for given parameter array.
     *
     * @param array $aParameters
     * @param bool  $addStateKey
     *
     * @return string
     *
     * @throws \InvalidArgumentException if the key cannot be created with the given parameters
     */
    public function getKey($aParameters, $addStateKey = true);

    /**
     * returns an array of the parameters relevant for caching from the request (such as language, currency, protocol, etc).
     *
     * @param Request $request
     *
     * @return array
     *
     * @deprecated since 6.2.0 - use chameleon_system_core.request_state_hash_provider instead.
     */
    public function getRequestStateKey(Request $request);
}
