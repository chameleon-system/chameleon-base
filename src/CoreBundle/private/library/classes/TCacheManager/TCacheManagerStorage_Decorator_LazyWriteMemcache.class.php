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
 * set to true if you want to collect the cache data in a dir and process as they come in
 * if you activate this, you will need to run /chameleon/blackbox/corescripts/TCacheManager_ProcessCacheMetaData.php via cron every 10 seconds or so to process
 * cache meta data.
 *
 * IMPORTANT: instead of using this, you should compile php so that pcntl_fork is defined (http://de2.php.net/manual/en/pcntl.installation.php)
 */
if (!defined('CHAMELEON_MEMCACHE_USE_LAZY_CACHE_WRITE_WRITEQUEUE')) {
    define('CHAMELEON_MEMCACHE_USE_LAZY_CACHE_WRITE_WRITEQUEUE', false);
}
/**
 * if you use a write queue, you will need to define a dir in which to collect the cache resources.
 */
if (!defined('CHAMELEON_MEMCACHE_USE_LAZY_CACHE_WRITE_WRITEQUEUE_DIR')) {
    define('CHAMELEON_MEMCACHE_USE_LAZY_CACHE_WRITE_WRITEQUEUE_DIR', PATH_CACHE.'/raw');
}

/**
 * @deprecated since 6.2.0 - no longer used.
 */
class TCacheManagerStorage_Decorator_LazyWriteMemcache extends TCacheManagerStorage_Decorator
{
    private $aCacheData = array();
    private $bDisableShutdown = false;

    /**
     * decorate a storage.
     *
     * @param ICacheManagerStorage $oStorage
     *
     * @return ICacheManagerStorage
     */
    public function Decorate(ICacheManagerStorage $oStorage)
    {
        parent::Decorate($oStorage);

        // register shutdown method
        register_shutdown_function(array($this, 'CommitLocalStorageTrigger'));

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function GetContents($sKey)
    {
        $oResult = $this->GetContentsLocal($sKey);
        if (false === $oResult) {
            $oResult = parent::GetContents($sKey);
        }

        return $oResult;
    }

    /**
     * {@inheritdoc}
     */
    public function SetContent($key, &$oContent, $aTableInfos = null, $isPage = false, $cleanKey = null, $iMaxLiveInSeconds = null)
    {
        $this->SetContentsLocal($key, $oContent, $aTableInfos, $isPage, $cleanKey, $iMaxLiveInSeconds);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function DeleteContent($key, $bIsInternalCall = false)
    {
        $this->DeleteContentsLocal($key);

        return parent::DeleteContent($key, $bIsInternalCall);
    }

    protected function SetContentsLocal($sKey, $oData, $aTrigger, $isPage, $cleanKey, $iMaxLiveInSeconds)
    {
        $this->aCacheData[$sKey] = array('oData' => $oData, 'aTrigger' => $aTrigger, 'isPage' => $isPage, 'cleanKey' => $cleanKey, 'iMaxLiveInSeconds' => $iMaxLiveInSeconds);
    }

    protected function GetContentsLocal($sKey)
    {
        if (isset($this->aCacheData[$sKey])) {
            return $this->aCacheData[$sKey]['oData'];
        } else {
            return false;
        }
    }

    protected function DeleteContentsLocal($sKey)
    {
        if (isset($this->aCacheData[$sKey])) {
            unset($this->aCacheData[$sKey]);
        }
    }

    /**
     * we want to execute CommitLocalStorage as the last shutdown function - so we use this trick to add it to the end of the queue.
     */
    public function CommitLocalStorageTrigger()
    {
        if (true === $this->bDisableShutdown) {
            return;
        }
        register_shutdown_function(array($this, 'CommitLocalStorage'));
    }

    public function CommitLocalStorage()
    {
        // run cache store in thread
        $bRunCacheStore = true;
        $bRunCacheViaFork = false;
        if (function_exists('pcntl_fork')) {
            // run clear in fork
            $pid = pcntl_fork();
            if ($pid <= 0) {
                $bRunCacheStore = true;
                $bRunCacheViaFork = true;
            } else {
                $bRunCacheStore = false;
            }
        }
        if (false === $bRunCacheStore) {
            return;
        }

        // commit everything to membase
        if (\ChameleonSystem\CoreBundle\ServiceLocator::getParameter('chameleon_system_core.cache.memcache_use_lazy_cache_write') && false === $bRunCacheViaFork) {
            $this->CommitLocalStorageFromArray($this->aCacheData, true, false);
            reset($this->aCacheData);
            foreach (array_keys($this->aCacheData) as $sKey) {
                unset($this->aCacheData[$sKey]['oData']);
            }
            $aContent = array();
            if (count($this->aCacheData) > 0) {
                $sDir = CHAMELEON_MEMCACHE_USE_LAZY_CACHE_WRITE_WRITEQUEUE_DIR.'/'.time().'_'.TTools::GetUUID().'.cache';
                $sData = serialize($this->aCacheData);
                file_put_contents($sDir, $sData);
            }
        } else {
            $this->CommitLocalStorageFromArray($this->aCacheData, false, false);
        }
    }

    protected function CommitLocalStorageFromArray($aData, $bIgnoreMeta = false, $bIgnoreCacheItems = false)
    {
        if (false === $bIgnoreCacheItems) {
            if (0 == count($aData)) {
                return;
            }
            reset($aData);
            $aContent = array();
            foreach (array_keys($aData) as $sKey) {
                $iExpire = $aData[$sKey]['oData'];
                $sTimeKey = 'x'.$iExpire;
                if (!isset($aContent[$sTimeKey])) {
                    $aContent[$sTimeKey] = array();
                }
                $aContent[$sTimeKey][$sKey] = $aData[$sKey]['oData'];
            }
            $oMemcache = $this->getMemcache();
            foreach (array_keys($aContent) as $sTimeKey) {
                if (false === $oMemcache->setMulti($aContent[$sTimeKey], (int) substr($sTimeKey, 1))) {
                    // unable to save - so remove meta as well
                    foreach (array_keys($aContent[$sTimeKey]) as $sKey) {
                        unset($aData[$sKey]);
                    }
                }
            }
        }

        if (false === $bIgnoreMeta) {
            // now write meta data
            reset($aData);

            foreach (array_keys($aData) as $sKey) {
                $this->WriteMetaData($sKey, $aData[$sKey]['aTrigger'], $aData[$sKey]['isPage'], $aData[$sKey]['cleanKey'], $aData[$sKey]['iMaxLiveInSeconds']);
            }
        }
    }

    public function ProcessCacheQueue()
    {
        $aFiles = array();
        $this->bDisableShutdown = true;
        $d = dir(CHAMELEON_MEMCACHE_USE_LAZY_CACHE_WRITE_WRITEQUEUE_DIR);
        while (false !== ($entry = $d->read())) {
            if ('.cache' == substr($entry, -6)) {
                $aFiles[] = $entry;
            }
        }
        $d->close();
        if (count($aFiles) > 0) {
            sort($aFiles);
            // reverse order
            array_reverse($aFiles);

            foreach ($aFiles as $sFile) {
                if (@rename(CHAMELEON_MEMCACHE_USE_LAZY_CACHE_WRITE_WRITEQUEUE_DIR.'/'.$sFile, CHAMELEON_MEMCACHE_USE_LAZY_CACHE_WRITE_WRITEQUEUE_DIR.'/_'.$sFile)) {
                    $aData = file_get_contents(CHAMELEON_MEMCACHE_USE_LAZY_CACHE_WRITE_WRITEQUEUE_DIR.'/_'.$sFile);
                    unlink(CHAMELEON_MEMCACHE_USE_LAZY_CACHE_WRITE_WRITEQUEUE_DIR.'/_'.$sFile);
                    $aData = unserialize($aData);
                    $this->CommitLocalStorageFromArray($aData, false, true);
                }
            }
        }
    }

    /**
     * @return TCMSMemcache
     */
    private function getMemcache()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_cms_cache.memcache_cache');
    }
}
