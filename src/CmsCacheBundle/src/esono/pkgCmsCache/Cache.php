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

use ChameleonSystem\CoreBundle\RequestState\Interfaces\RequestStateHashProviderInterface;
use ChameleonSystem\CoreBundle\Util\HashInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Symfony\Component\HttpFoundation\RequestStack;

class Cache implements CacheInterface
{
    public const REQUEST_STATE_HASH = '__requestStateHash';
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var Connection
     */
    private $dbConnection;
    /**
     * @var StorageInterface|null
     */
    private $storage;
    /**
     * @var bool
     */
    private $isActive = true;
    /**
     * @var string
     */
    private $cacheKeyPrefix;
    /**
     * @var bool
     */
    private $cacheAllowed;
    /**
     * @var HashInterface
     */
    private $hashArray;
    /**
     * @var RequestStateHashProviderInterface
     */
    private $requestStateHashProvider;

    /**
     * @param string $cacheKeyPrefix
     * @param bool $cacheAllowed
     */
    public function __construct(
        RequestStack $requestStack,
        Connection $dbConnection,
        StorageInterface $oCacheStorage,
        $cacheKeyPrefix,
        $cacheAllowed,
        HashInterface $hashArray,
        RequestStateHashProviderInterface $requestStateHashProvider
    ) {
        $this->setRequestStack($requestStack);
        $this->setDbConnection($dbConnection);
        $this->setStorage($oCacheStorage);
        $this->cacheKeyPrefix = $cacheKeyPrefix;
        $this->cacheAllowed = $cacheAllowed;
        $this->hashArray = $hashArray;
        $this->requestStateHashProvider = $requestStateHashProvider;
    }

    /**
     * @return void
     */
    public function setStorage(StorageInterface $oCacheStorage)
    {
        $this->storage = $oCacheStorage;
    }

    /**
     * {@inheritdoc}
     */
    public function disable()
    {
        $this->isActive = false;
    }

    /**
     * {@inheritdoc}
     */
    public function enable()
    {
        $this->isActive = true;
    }

    /**
     * {@inheritdoc}
     */
    public function isActive()
    {
        return $this->cacheAllowed && $this->isActive;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        if (false === $this->isActive()) {
            return null;
        }

        return $this->storage->get($key);
    }

    /**
     * adds or updates a cache object.
     *
     * @param string $key - the cache key
     * @param mixed $content - object to be stored
     * @param array $trigger - cache trigger array(array('table'=>'','id'=>''),array('table'=>'','id'=>''),...);
     * @param int $iMaxLiveInSeconds - max age in seconds before the cache content expires - default = never. please note that the value must not exceed 30 days (see http://www.php.net/manual/en/memcached.expiration.php)
     */
    public function set($key, $content, $trigger, $iMaxLiveInSeconds = null)
    {
        if (false === $this->isActive()) {
            return;
        }
        $this->storage->set($key, $content, $iMaxLiveInSeconds);
        $this->setTrigger($trigger, $key);
    }

    /**
     * sets the triggers that will delete the cache object identified by $key.
     *
     * @param array $triggerList
     * @param string $key
     *
     * @return void
     */
    private function setTrigger($triggerList, $key)
    {
        if (!is_array($triggerList) || 0 === count($triggerList)) {
            return;
        }
        $this->getDbConnection()->beginTransaction();
        $triggerGroup = $this->getTriggerGroup($triggerList);
        $aTriggerWrite = [];
        foreach ($triggerList as $trigger) {
            $idList = '';
            $table = '';
            if (is_array($trigger)) {
                $table = $trigger['table'];
                if (isset($trigger['id'])) {
                    $idList = $trigger['id'];
                }
            } else {
                $table = $trigger;
            }
            if (!is_array($idList)) {
                $idList = [$idList];
            }
            foreach ($idList as $id) {
                if ('' === $id) {
                    $id = '<ALL>';
                }
                $aTriggerWrite[] = "{$table}={$id}";
            }
        }

        $sTriggerList = '|'.implode('|', $aTriggerWrite).'|';

        $query = "INSERT IGNORE INTO `_cms_cache_info`
                         SET `cms_cache_key` = '{$key}',
                             `table` = '{$sTriggerList}',
                             `groupid` = '{$triggerGroup}'
        ";
        $this->getDbConnection()->executeQuery($query);
        $this->getDbConnection()->commit();
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        if (false === $this->isActive()) {
            return false;
        }

        return $this->storage->delete($key);
    }

    /**
     * {@inheritdoc}
     */
    public function clearAll()
    {
        if (false === $this->isActive()) {
            return;
        }
        $this->getDbConnection()->beginTransaction();
        $query = 'DELETE FROM `_cms_cache_info`';
        $this->getDbConnection()->executeQuery($query);
        $query = 'DELETE FROM `_cms_cache_group`';
        try {
            $this->getDbConnection()->executeQuery($query);
        } catch (DBALException $e) {
            // the table does not exist yet
        }
        $this->getDbConnection()->commit();
        $this->storage->clearAll();
    }

    /**
     * {@inheritdoc}
     */
    public function callTrigger($table, $id = null)
    {
        if (CHAMELEON_CACHE_ENABLE_CACHE_INFO === false) {
            return;
        }
        if (false === $this->isActive()) {
            return;
        }
        // we use the group to reduce the list form which to select...
        $query = 'SELECT * FROM _cms_cache_group WHERE `tables` LIKE ?';
        try {
            $stmt = $this->getDbConnection()->prepare($query);
            $stmt->bindValue(1, '%|'.$table.'|%');
            $result = $stmt->executeQuery();
        } catch (DBALException $e) {
            return;
        }

        $aSelect = [];
        while ($group = $result->fetchAssociative()) {
            $groupId = $group['id'];
            if (null === $id || '' === $id) {
                $aSelect[] = "SELECT cms_cache_key FROM _cms_cache_info WHERE `groupid` = '{$groupId}' AND (`table` LIKE '%|{$table}=%|%')";
            } else {
                $aSelect[] = "SELECT cms_cache_key FROM _cms_cache_info WHERE `groupid` = '{$groupId}' AND (`table` LIKE '%|{$table}={$id}|%' OR `table` LIKE '%|{$table}=<ALL>|%')";
            }
        }
        if (0 === count($aSelect)) {
            return;
        }
        $fullSelect = implode(' UNION DISTINCT ', $aSelect);

        $result = $this->getDbConnection()->executeQuery($fullSelect);

        while ($trigger = $result->fetchAssociative()) {
            $this->delete($trigger['cms_cache_key']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getKey($aParameters, $addStateKey = true)
    {
        if ($addStateKey) {
            $aParameters['__state'] = [
                self::REQUEST_STATE_HASH => $this->requestStateHashProvider->getHash($this->requestStack->getCurrentRequest()),
            ];
        }
        $aParameters['__uniqueIdentity'] = $this->cacheKeyPrefix;

        return $this->hashArray->hash32($aParameters);
    }

    /**
     * @return void
     */
    public function setRequestStack(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @return Connection
     */
    private function getDbConnection()
    {
        return $this->dbConnection;
    }

    /**
     * @return void
     */
    public function setDbConnection(Connection $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    /**
     * @param array $aTriggerList
     *
     * @return string
     *
     * @throws DBALException
     */
    private function getTriggerGroup($aTriggerList)
    {
        $aTableList = [];
        foreach ($aTriggerList as $aTrigger) {
            if (is_array($aTrigger)) {
                $aTableList[] = $aTrigger['table'];
            } else {
                $aTableList[] = $aTrigger;
            }
        }
        if (count($aTableList) > 1) {
            $aTableList = array_unique($aTableList);
            sort($aTableList);
            $sTableList = implode('|', $aTableList);
        } else {
            $sTableList = $aTableList[0];
        }
        $sTableList = '|'.$sTableList.'|';
        $group = md5($sTableList);
        $query = "INSERT IGNORE INTO `_cms_cache_group`
                                 SET `id` = '{$group}',
                                     `tables` = '{$sTableList}'
                 ";
        $this->getDbConnection()->executeQuery($query);

        return $group;
    }
}
