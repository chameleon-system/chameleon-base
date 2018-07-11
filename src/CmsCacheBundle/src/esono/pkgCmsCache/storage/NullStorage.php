<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace esono\pkgCmsCache\storage;

use esono\pkgCmsCache\StorageInterface;

class NullStorage implements StorageInterface
{
    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function clearAll()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $expireInSeconds = 0)
    {
        return true;
    }
}
