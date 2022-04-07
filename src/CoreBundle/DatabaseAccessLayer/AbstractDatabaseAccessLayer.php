<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\DatabaseAccessLayer;

use Doctrine\DBAL\Connection;

abstract class AbstractDatabaseAccessLayer
{
    /**
     * @var Connection
     */
    private $databaseConnection;

    /**
     * @var array<string, mixed>
     */
    private $cache = array();

    /**
     * @var array<string, string>
     */
    private $keyMapping = array();

    /**
     * @param Connection $connection
     *
     * @return void
     */
    public function setDatabaseConnection(Connection $connection)
    {
        $this->databaseConnection = $connection;
    }

    /**
     * @param string $field
     * @param string $value
     * @param null|string $languageId
     *
     * @return list<\TCMSRecord|null>
     */
    protected function findDbObjectFromFieldInCache($field, $value, $languageId = null)
    {
        $results = array();
        reset($this->cache);
        $compareValue = (string) $value;
        foreach ($this->cache as $cacheKey => $cacheValue) {
            if (false === ($this->cache[$cacheKey] instanceof \TCMSRecord)) {
                continue;
            }
            if (isset($this->cache[$cacheKey]->sqlData[$field]) && ((string) $this->cache[$cacheKey]->sqlData[$field]) === $compareValue) {
                $results[] = $this->cache[$cacheKey];
            }
        }
        reset($this->cache);

        return $results;
    }

    /**
     * @return Connection
     */
    protected function getDatabaseConnection()
    {
        return $this->databaseConnection;
    }

    /**
     * @param string $id
     * @param mixed $object
     *
     * @return void
     */
    protected function setCache($id, $object)
    {
        $this->cache[$id] = $object;
    }

    /**
     * @param string $id
     *
     * @return mixed|null
     */
    protected function getFromCache($id)
    {
        return (isset($this->cache[$id])) ? $this->cache[$id] : null;
    }

    /**
     * @param string $mappedKey
     *
     * @return mixed|null
     */
    protected function getFromCacheViaMappedKey($mappedKey)
    {
        if (isset($this->keyMapping[$mappedKey])) {
            return $this->getFromCache($this->keyMapping[$mappedKey]);
        }

        return null;
    }

    /**
     * @param array $cacheLookupData
     *
     * @return void
     */
    private function normalize(&$cacheLookupData)
    {
        foreach ($cacheLookupData as $key => $value) {
            if (is_array($cacheLookupData[$key])) {
                $this->normalize($cacheLookupData[$key]);
            }
        }
        ksort($cacheLookupData);
    }

    /**
     * @param string $mappedKey
     * @param string $id
     *
     * @return void
     */
    protected function setCacheKeyMapping($mappedKey, $id)
    {
        $this->keyMapping[$mappedKey] = $id;
    }

    /**
     * @param array $cacheLookupData
     *
     * @return string
     */
    protected function getMapLookupKey(array $cacheLookupData)
    {
        $this->normalize($cacheLookupData);

        return md5(serialize($cacheLookupData));
    }

    /**
     * @return array
     */
    protected function getCompleteCache()
    {
        return $this->cache;
    }
}
