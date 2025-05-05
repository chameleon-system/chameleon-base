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

class Memcache implements StorageInterface
{
    /**
     * @var \TCMSMemcache
     */
    private $memcache;

    public function __construct(\TCMSMemcache $memcache)
    {
        $this->memcache = $memcache;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        $content = $this->memcache->Get($key);
        if (true === $this->memcache->getLastGetRequestReturnedNoMatch()) {
            return null;
        }

        return $content;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        return $this->memcache->Delete($key);
    }

    /**
     * {@inheritdoc}
     */
    public function clearAll()
    {
        return $this->memcache->Flush();
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $expireInSeconds = 0)
    {
        return $this->memcache->Set($key, $value, $expireInSeconds);
    }
}
