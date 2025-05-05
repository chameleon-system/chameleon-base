<?php

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * The AtomicLock Class allows you to acquire a temporary lock bound to an id/key.
 * The way you should use it is as follow:.
 *
 * $oAtomicLock = new AtomicLock();
 * $oLock = $oAtomicLock->acquireLock("mykey");
 * if(null === $oLock){
 *   // already locked from another process
 * } else {
 *   // do lockworthy stuff
 *   $oLock->release();
 * }
 *
 * The Lock is being acquired atomically, so there will always be only one lock
 * per given id.
 *
 * There is a CronJob, that currently kills all locks every 10 minutes.
 * This should not be the final solution, as it possible, that in a
 * very unlikely case, the atomic nature will break because of this.
 */
class AtomicLock
{
    /**
     * @var string
     */
    private static $LOCK_TABLE = 'data_atomic_lock';

    /**
     * @var string
     */
    private static $LOCK_FIELD = 'lockkey';

    /**
     * @var string|null
     */
    private $key;

    /**
     * Helps in getting a unique key for a given object.
     *
     * @return string key you can use for acquireLock()
     */
    public function getKeyForObject($oObject)
    {
        return md5(serialize($oObject));
    }

    /**
     * Acquire a lock for the given key.
     *
     * @param string $key
     *
     * @return static|null if the lock has been created successfully. Null, if there is already an active lock for this key
     */
    public function acquireLock($key)
    {
        if (null !== $this->key) {
            return null;
        }

        try {
            $this->getDatabaseConnection()->insert(self::$LOCK_TABLE, [
                'id' => TTools::GetUUID(),
                self::$LOCK_FIELD => $key,
            ]);
            $this->key = $key;

            return $this;
        } catch (DBALException $e) {
            return null;
        }
    }

    /**
     * Releases the lock.
     *
     * @return bool if the release was successful
     */
    public function release()
    {
        if (null === $this->key) {
            return false;
        }
        try {
            $this->getDatabaseConnection()->delete(self::$LOCK_TABLE, [
                self::$LOCK_FIELD => $this->key,
            ]);
        } catch (DBALException $e) {
            // mimicking the old behaviour by ignoring the exception.
        }

        return true;
    }

    /**
     * @return Connection
     */
    private function getDatabaseConnection()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('database_connection');
    }
}
