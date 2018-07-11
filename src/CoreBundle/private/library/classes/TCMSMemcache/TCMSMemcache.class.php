<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\TCMSMemcache\FalseValueCacheEntry;

class TCMSMemcache
{
    /**
     * holds memcache instance depending on what is available - for now only memcached.
     *
     * @var null|Memcached
     */
    protected $oMemcache = null;

    /**
     * the used memcache class as string.
     *
     * @var string
     *
     * @deprecated since 6.2.0 - only Memcached is supported.
     */
    protected $sUsedMemcacheClass = 'Memcached';

    /**
     * enable or disable logging - log messages are written into normal log file (e.g. chameleon.log by default).
     *
     * @var bool
     */
    protected $bLogging = false;

    /**
     * this array describes what we want to log
     * possible values are:
     * 'set',
     * 'replace',
     * 'get',
     * 'delete',
     * 'flush'
     * by default you only should enable flush - believe me ... only enable other methods for debugging purposes
     * otherwise you chameleon.log will be spammed.
     *
     * @var array
     */
    protected $aLoggingMethods = array('flush');

    /**
     * @deprecated since 6.2.0 - no longer supported.
     */
    const CACHE_DRIVER_MEMCACHE = 1;
    /**
     * @deprecated since 6.2.0 - no longer needed, as Memcached is the only supported driver.
     */
    const CACHE_DRIVER_MEMCACHED = 2;
    /**
     * @var bool
     */
    private $lastGetRequestReturnedNoMatch = false;
    /**
     * @var int
     */
    private $timeout;

    /**
     * @param string $memcacheClass @deprecated since 6.2.0 - only Memcached is supported.
     * @param int    $timeout
     * @param array  $serverList
     */
    public function __construct($memcacheClass, $timeout, array $serverList)
    {
        $this->timeout = $timeout;

        $this->Init($memcacheClass);

        $serversToUseList = array();
        foreach ($serverList as $server) {
            if (false !== $server['host']) {
                $serversToUseList[] = $server;
            }
        }
        $this->SetServer($serversToUseList);
        $this->PostInit();
    }

    /**
     * searches for the memcache class that should be used.
     *
     * if you think you have connection issues when using memcached lib
     * use some of the following methods for debugging after the addServer method was executed
     * $this->oMemcache->getResultCode(); - should be 0
     * $this->oMemcache->getResultMessage(); - should be success
     * $this->oMemcache->getStats(); - should be an array with server information
     *
     * @param string $memcacheClass @deprecated since 6.2.0 - only Memcached is supported.
     */
    public function Init($memcacheClass = 'auto')
    {
    }

    /**
     * will be executed after Init() and SetServer() method to check some stuff that possibly go wrong
     * so here you can write logs or throw errors or do whatever you want after initialization is done
     * by default triggers an error if no memcache could be instantiated.
     */
    public function PostInit()
    {
        if (null === $this->oMemcache) {
            trigger_error("couldn't get memcached instance", E_USER_ERROR);
        }
    }

    /**
     * initializes the memcache object $this->oMemcache depending on what class to use and sets $this->sUsedMemcacheClass.
     *
     * @param array $aServer - server array can hold as much servers as you want (as array) each server array must have the keys 'host' and 'port'
     */
    protected function SetServer($aServer)
    {
        if (false === class_exists(Memcached::class)) {
            return;
        }
        $this->oMemcache = new Memcached();
        $this->oMemcache->setOption(\Memcached::OPT_CONNECT_TIMEOUT, $this->timeout);
        if (count($aServer) > 1) {
            $iWeight = 100 / count($aServer);
            foreach ($aServer as $aServerInfo) {
                if (array_key_exists('host', $aServerInfo) && array_key_exists('port', $aServerInfo)) {
                    $this->oMemcache->addServer($aServerInfo['host'], $aServerInfo['port'], $iWeight);
                }
            }
        } elseif (1 == count($aServer)) {
            $aServerInfo = current($aServer);
            if (array_key_exists('host', $aServerInfo) && array_key_exists('port', $aServerInfo)) {
                $this->oMemcache->addServer($aServerInfo['host'], $aServerInfo['port']);
            }
        } else {
            $this->oMemcache = null;
        }
    }

    /**
     * returns a initialized TCMSMemcache object.
     *
     * @return TCMSMemcache
     *
     * @deprecated inject chameleon_system_cms_cache.memcache_cache instead
     */
    public static function GetCacheInstance()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_cms_cache.memcache_cache');
    }

    /**
     * returns a initialized TCMSMemcache object.
     *
     * @return TCMSMemcache
     *
     * @deprecated inject chameleon_system_cms_cache.memcache_session instead
     */
    public static function GetSessionInstance()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_cms_cache.memcache_session');
    }

    /**
     * writes a value for given key into memcache.
     *
     * @param string $sKey
     * @param mixed  $mValue
     * @param int    $iExpire - 0 means never expire
     *
     * @return bool
     */
    public function Set($sKey, $mValue, $iExpire = 0)
    {
        $mValue = $this->processBeforeWrite($mValue);
        $bIsSet = $this->oMemcache->set($sKey, $mValue, $iExpire);

        if (false == $bIsSet && $this->bLogging && in_array('set', $this->aLoggingMethods)) {
            TTools::WriteLogEntry('memcached set for key: '.$sKey.' with expire: '.$iExpire.' failed - memcached result code was: '.$this->oMemcache->getResultCode().' result message: '.$this->oMemcache->getResultMessage(), 2, __FILE__, __LINE__);
        }

        return $bIsSet;
    }

    public function setMulti($aItems, $iExpire = 0)
    {
        foreach ($aItems as $key => $item) {
            $aItems[$key] = $this->processBeforeWrite($item);
        }
        $bIsSet = $this->oMemcache->setMulti($aItems, $iExpire);

        if (false == $bIsSet && $this->bLogging && in_array('setMulti', $this->aLoggingMethods)) {
            TTools::WriteLogEntry('memcached setMulti for keys: '.implode(', ', array_keys($aItems)).' with expire: '.$iExpire.' failed - memcached result code was: '.$this->oMemcache->getResultCode().' result message: '.$this->oMemcache->getResultMessage(), 2, __FILE__, __LINE__);
        }

        return $bIsSet;
    }

    /**
     * tries to replace a value for given key in memcache.
     *
     * @param string $sKey
     * @param mixed  $mValue
     * @param int    $iExpire - 0 means never expire
     *
     * @return bool
     */
    public function Replace($sKey, $mValue, $iExpire = 0)
    {
        $mValue = $this->processBeforeWrite($mValue);
        $bIsSet = $this->oMemcache->replace($sKey, $mValue, $iExpire);

        if (false == $bIsSet && $this->bLogging && in_array('set', $this->aLoggingMethods)) {
            TTools::WriteLogEntry('memcached set for key: '.$sKey.' with expire: '.$iExpire.' failed - memcached result code was: '.$this->oMemcache->getResultCode().' result message: '.$this->oMemcache->getResultMessage(), 2, __FILE__, __LINE__);
        }

        return $bIsSet;
    }

    /**
     * fetches a value for given key.
     *
     * @param string $sKey
     *
     * @return mixed - on fail return false
     */
    public function Get($sKey)
    {
        $mValue = $this->oMemcache->get($sKey);
        $mValue = $this->processPostRead($mValue);
        if (true === $this->getLastGetRequestReturnedNoMatch() && $this->bLogging && in_array('get', $this->aLoggingMethods)) {
            TTools::WriteLogEntry('memcached get for key: '.$sKey.' failed - memcached result code was: '.$this->oMemcache->getResultCode().' result message: '.$this->oMemcache->getResultMessage(), 2, __FILE__, __LINE__);
        }

        return $mValue;
    }

    /**
     * deletes a stored value in memcache for given key.
     *
     * @param string $sKey
     *
     * @return bool
     */
    public function Delete($sKey)
    {
        $bDeleted = $this->oMemcache->delete($sKey);
        $bDeleted = ($bDeleted || (16 == $this->oMemcache->getResultCode())); // delete is ok if key was not found

        if (false === $bDeleted && $this->bLogging && in_array('delete', $this->aLoggingMethods)) {
            $iResultCode = $this->oMemcache->getResultCode();
            TTools::WriteLogEntry('memcached delete for key: '.$sKey.' failed - memcached result code was: '.$iResultCode.' result message: '.$this->oMemcache->getResultMessage(), 2, __FILE__, __LINE__);
        }

        return $bDeleted;
    }

    /**
     * flushes the complete memcache.
     *
     * @return bool
     */
    public function Flush()
    {
        if (!$this->oMemcache->flush()) {
            if ($this->bLogging && in_array('flush', $this->aLoggingMethods)) {
                $iResultCode = $this->oMemcache->getResultCode();
                TTools::WriteLogEntry('memcached flush failed - memcached result code was: '.$iResultCode.' result message: '.$this->oMemcache->getResultMessage(), 2, __FILE__, __LINE__);
            }

            return false;
        }

        return true;
    }

    /**
     * @return int self::CACHE_DRIVER_MEMCACHED
     *
     * @deprecated since 6.2.0 - only Memcached is supported.
     */
    public function getDriverType()
    {
        return self::CACHE_DRIVER_MEMCACHED;
    }

    /**
     * @return Memcached|null
     */
    public function getDriver()
    {
        return $this->oMemcache;
    }

    private function processBeforeWrite($value)
    {
        if (false === $value) {
            $value = new FalseValueCacheEntry();
        }

        return $value;
    }

    private function processPostRead($value)
    {
        $this->lastGetRequestReturnedNoMatch = false;
        if ($value instanceof FalseValueCacheEntry) {
            return false;
        }
        if (false === $value) {
            $this->lastGetRequestReturnedNoMatch = true;
        }

        return $value;
    }

    /**
     * @return bool
     */
    public function getLastGetRequestReturnedNoMatch()
    {
        return $this->lastGetRequestReturnedNoMatch;
    }
}
