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

interface StorageInterface
{
    /**
     * @param string $key
     *
     * @return string|null
     */
    public function get($key);

    /**
     * @param string $key
     * @param int $expireInSeconds
     *
     * @return bool
     */
    public function set($key, $value, $expireInSeconds = 0);

    /**
     * @param string $key
     *
     * @return bool
     */
    public function delete($key);

    /**
     * @return bool
     */
    public function clearAll();
}
