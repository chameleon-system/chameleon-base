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
 * @deprecated since 6.2.0 - no longer used.
 */
class TCacheManagerStorage_Standard implements ICacheManagerStorage
{
    /**
     * {@inheritdoc}
     */
    public function GetContents($sKey)
    {
        $result = false;
        $memcacheActive = \ChameleonSystem\CoreBundle\ServiceLocator::getParameter('chameleon_system_core.cache.memcache_activate');
        if ($memcacheActive) {
            try {
                /** @var $oMemcache TCMSMemcache */
                $oMemcache = &TGlobal::GetMemcacheInstance();
                if ($oMemcache) {
                    $result = @$oMemcache->Get($sKey);
                }
            } catch (Exception $e) {
                $this->DeleteContent($sKey);
                $result = false;
            }
        }

        $memcacheUseFallback = \ChameleonSystem\CoreBundle\ServiceLocator::getParameter('chameleon_system_core.cache.memcache_use_fallback');
        if ((!$memcacheActive || ($memcacheUseFallback && false === $result))) {
            $storeInFileSystem = \ChameleonSystem\CoreBundle\ServiceLocator::getParameter('chameleon_system_core.cache.use_file_system_as_standard_cache');
            if ($storeInFileSystem) {
                $sFile = $this->GetFilePath($sKey);
                if (file_exists($sFile)) {
                    $sContent = file_get_contents($sFile);
                    try {
                        $result = $sContent; // supress notice!
                    } catch (Exception $e) {
                        // remove cache object, and return false
                        $this->DeleteContent($sKey);
                        $result = false;
                    }
                }
            } else { // use db
                $query = 'SELECT `content` FROM `'.TCacheManager::GetCacheTable()."` WHERE `key` = '".MySqlLegacySupport::getInstance()->real_escape_string($sKey)."' AND `marked_for_delete` = 0";
                if ($aContent = @$this->getCacheDatabaseConnection()->fetchArray($query)) {
                    try {
                        $result = $aContent['0']; // supress notice!
                    } catch (Exception $e) {
                        // remove cache object, and return false
                        $this->DeleteContent($sKey);
                        $result = false;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function SetContent($sKey, &$oContent, $aTableInfos = null, $isPage = false, $cleanKey = null, $iMaxLiveInSeconds = null)
    {
        $bCacheStored = false;
        $memcacheActive = \ChameleonSystem\CoreBundle\ServiceLocator::getParameter('chameleon_system_core.cache.memcache_activate');

        if ($memcacheActive) {
            /** @var $oMemcache TCMSMemcache */
            $oMemcache = &TGlobal::GetMemcacheInstance();
            if ($oMemcache) {
                if ($oMemcache->Set($sKey, $oContent)) {
                    $bCacheStored = true;
                }
            }
        }
        $tmpData = '';
        $memcacheUseFallback = \ChameleonSystem\CoreBundle\ServiceLocator::getParameter('chameleon_system_core.cache.memcache_use_fallback');
        if (!$memcacheActive || (!$bCacheStored && $memcacheUseFallback)) {
            $bCacheStored = true;
            $storeInFileSystem = \ChameleonSystem\CoreBundle\ServiceLocator::getParameter('chameleon_system_core.cache.use_file_system_as_standard_cache');
            if ($storeInFileSystem) {
                $sFilePath = $this->GetFilePath($sKey);
                $sTmpFile = $sFilePath.'-'.TTools::GetUUID().'.tmp';

                $tmpData = $oContent;
                $fp = fopen($sTmpFile, 'wb');
                fwrite($fp, $tmpData);
                fclose($fp);
                rename($sTmpFile, $sFilePath);
                $tmpData = '';
            } else {
                $tmpData = $oContent;
            }
        }

        if ($bCacheStored) {
            $bCacheStored = $this->WriteMetaData($sKey, $aTableInfos, $isPage, $cleanKey, $iMaxLiveInSeconds, $tmpData);
        }

        return $bCacheStored;
    }

    /**
     * {@inheritdoc}
     */
    public function WriteMetaData($sKey, $aTableInfos = null, $isPage = false, $cleanKey = null, $iMaxLiveInSeconds = null, $sCacheContent = '')
    {
        if (CHAMELEON_CACHE_ENABLE_CACHE_INFO === false && true === \ChameleonSystem\CoreBundle\ServiceLocator::getParameter('chameleon_system_core.cache.memcache_activate')) {
            return true;
        }
        $cacheDatabaseConnection = $this->getCacheDatabaseConnection();
        $pagecache = 0;
        if ($isPage) {
            $pagecache = 1;
        }
        $iExpireTimestamp = $iMaxLiveInSeconds + time();

        $aCacheTriggerTmp = array();
        $aInsertCacheTriggerSQL = array(); // used to build the insert for the _cms_cache_info table

        if (!is_null($aTableInfos)) {
            foreach ($aTableInfos as $aTableInfo) {
                $sTmpTableName = $aTableInfo['table'];
                if (array_key_exists('id', $aTableInfo)) {
                    if (is_array($aTableInfo['id'])) {
                        foreach ($aTableInfo['id'] as $sTmpId) {
                            $aCacheTriggerTmp[] = $sTmpTableName.'-'.$sTmpId;
                            $aInsertCacheTriggerSQL[] = "('@@GLOBAL-TRIGGER-KEY@@', '".MySqlLegacySupport::getInstance()->real_escape_string($sTmpTableName)."','".MySqlLegacySupport::getInstance()->real_escape_string($sTmpId)."')";
                        }
                    } else {
                        $aCacheTriggerTmp[] = $sTmpTableName.'-'.$aTableInfo['id'];
                        $aInsertCacheTriggerSQL[] = "('@@GLOBAL-TRIGGER-KEY@@', '".MySqlLegacySupport::getInstance()->real_escape_string($sTmpTableName)."','".MySqlLegacySupport::getInstance()->real_escape_string($aTableInfo['id'])."')";
                    }
                } else {
                    $aCacheTriggerTmp[] = $sTmpTableName.'-';
                    $aInsertCacheTriggerSQL[] = "('@@GLOBAL-TRIGGER-KEY@@', '".MySqlLegacySupport::getInstance()->real_escape_string($sTmpTableName)."','')";
                }
            }
            sort($aCacheTriggerTmp);
        }
        $sCacheTriggerKey = md5(implode(';', $aCacheTriggerTmp));

        // now check if we need to save anything
        $query = 'SELECT * FROM `'.TCacheManager::GetCacheInfoTable().'`
                WHERE `'.TCacheManager::GetCacheInfoTable()."`.`cms_cache_key` = '".MySqlLegacySupport::getInstance()->real_escape_string($sCacheTriggerKey)."' LIMIT 0,1";
        $tRes = $cacheDatabaseConnection->query($query);
        $bCacheTriggerExists = ($tRes->rowCount() > 0);

        $query = 'INSERT INTO `'.TCacheManager::GetCacheTable()."`
                SET `key` = '".MySqlLegacySupport::getInstance()->real_escape_string($sKey)."',
                    `content` = '".MySqlLegacySupport::getInstance()->real_escape_string($sCacheContent)."',
                    `created_timestamp` = ".time().",
                    `expire_timestamp` = '".MySqlLegacySupport::getInstance()->real_escape_string($iExpireTimestamp)."',
                    `pagecache` = ".$pagecache.",
                    `trigger_key` = '".MySqlLegacySupport::getInstance()->real_escape_string($sCacheTriggerKey)."'
       ";
        if (!is_null($cleanKey)) {
            $query .= ", `cleankey` = '".MySqlLegacySupport::getInstance()->real_escape_string($cleanKey)."'";
        }
        $cacheDatabaseConnection->query($query);
        $bSuccessWriteCacheEntry = $this->WriteCacheEntrySuccessful(TCacheManager::GetCacheTable(), $sCacheTriggerKey);
        $bSuccessWriteCacheEntryInfo = true;
        if (CHAMELEON_CACHE_ENABLE_CACHE_INFO === false) {
            return $bSuccessWriteCacheEntry && $bSuccessWriteCacheEntryInfo;
        }
        if (!$bCacheTriggerExists && is_array($aTableInfos) && count($aTableInfos) > 0 && $bSuccessWriteCacheEntry) {
            $sInsertString = implode(', ', $aInsertCacheTriggerSQL);
            $sInsertString = str_replace("'@@GLOBAL-TRIGGER-KEY@@'", "'".MySqlLegacySupport::getInstance()->real_escape_string($sCacheTriggerKey)."'", $sInsertString);
            $query = 'INSERT INTO `'.TCacheManager::GetCacheInfoTable().'` (`cms_cache_key`,`table`,`id`)
VALUES '.$sInsertString;

            $cacheDatabaseConnection->query($query);
            $bSuccessWriteCacheEntryInfo = $this->WriteCacheEntrySuccessful(TCacheManager::GetCacheInfoTable(), $sCacheTriggerKey);
        }

        return $bSuccessWriteCacheEntry && $bSuccessWriteCacheEntryInfo;
    }

    /**
     * {@inheritdoc}
     */
    public function DeleteContent($sKey, $bIsInternalCall = false)
    {
        $bExternalDeleteSuccess = false;
        $memcacheActive = \ChameleonSystem\CoreBundle\ServiceLocator::getParameter('chameleon_system_core.cache.memcache_activate');

        // try memcached delete...
        if ($memcacheActive) {
            /** @var $oMemcache TCMSMemcache */
            $oMemcache = &TGlobal::GetMemcacheInstance();
            if ($oMemcache) {
                $bExternalDeleteSuccess = $oMemcache->Delete($sKey);
            }
        } else {
            $bExternalDeleteSuccess = true;
        }

        if (false === $bIsInternalCall && true === $bExternalDeleteSuccess) {
            $query = 'UPDATE `'.TCacheManager::GetCacheTable().'`
               SET `marked_for_delete` = 1,
                   `mark_for_delete_timestamp` = '.time().",
                   `delete_context` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->GetDeleteContext())."'
             WHERE `key` = '".MySqlLegacySupport::getInstance()->real_escape_string($sKey)."'
           ";
            $this->getCacheDatabaseConnection()->query($query);
        }

        if ($bIsInternalCall) {
            $memcacheUseFallback = \ChameleonSystem\CoreBundle\ServiceLocator::getParameter('chameleon_system_core.cache.memcache_use_fallback');
            $storeInFileSystem = \ChameleonSystem\CoreBundle\ServiceLocator::getParameter('chameleon_system_core.cache.use_file_system_as_standard_cache');
            if ($storeInFileSystem && (!$memcacheActive || $memcacheUseFallback)) {
                $sFile = $this->GetFilePath($sKey);
                @unlink($sFile);
            }
        }

        return $bExternalDeleteSuccess;
    }

    /**
     * return the delete context.
     *
     * @return string
     */
    protected function GetDeleteContext()
    {
        $sDeleteContext = '';
        if (\ChameleonSystem\CoreBundle\ServiceLocator::getParameter('chameleon_system_core.cache.include_cache_delete_trace_info')) {
            $sDeleteContext = TTools::GetFormattedDebug();
        }

        return $sDeleteContext;
    }

    protected function GetFilePath($sKey)
    {
        $sDir = PATH_CACHE;
        $sOffset = substr($sKey, 0, 2);
        $sDir = $sDir.$sOffset;
        if (!is_dir($sDir)) {
            mkdir($sDir, 0777, true);
        }

        return $sDir.'/'.$sKey.'.txt';
    }

    /**
     * Checks if last cache insert query was successful.
     * Could happen if memory cache table size is too small.
     *
     * Parameters are for future purposes
     *
     * @static
     *
     * @param $sTableName
     * @param $sCacheKey
     *
     * @return bool
     */
    protected function WriteCacheEntrySuccessful($sTableName, $sCacheKey)
    {
        $bSuccess = true;
        $sMySqlError = MySqlLegacySupport::getInstance()->error();
        $sMySqlErrorNo = MySqlLegacySupport::getInstance()->errno();
        if ('' != $sMySqlError && '' != $sMySqlErrorNo) {
            $bSuccess = false;
        }

        return $bSuccess;
    }

    /**
     * {@inheritdoc}
     */
    public function ClearCache()
    {
        $dTime = time();
        $cacheDatabaseConnection = $this->getCacheDatabaseConnection();
        $memcacheActive = \ChameleonSystem\CoreBundle\ServiceLocator::getParameter('chameleon_system_core.cache.memcache_activate');
        $storeInFileSystem = \ChameleonSystem\CoreBundle\ServiceLocator::getParameter('chameleon_system_core.cache.use_file_system_as_standard_cache');

        if ($memcacheActive) {
            // force clear cache
            $query = 'truncate `'.TCacheManager::GetCacheTable().'`';
            $cacheDatabaseConnection->query($query);
            $query = 'truncate `'.TCacheManager::GetCacheInfoTable().'`';

            $cacheDatabaseConnection->query($query);
            /** @var $oMemcache TCMSMemcache */
            $oMemcache = &TGlobal::GetMemcacheInstance();
            if ($oMemcache) {
                $oMemcache->Flush();
            }
            // wait 1 second
            sleep(1);
        } elseif (!$storeInFileSystem) {
            $query = 'truncate `'.TCacheManager::GetCacheTable().'`';
            $cacheDatabaseConnection->query($query);
            $query = 'truncate `'.TCacheManager::GetCacheInfoTable().'`';
            $cacheDatabaseConnection->query($query);
        } elseif ($storeInFileSystem) {
            $query = 'truncate `'.TCacheManager::GetCacheTable().'`';
            $cacheDatabaseConnection->query($query);
            $query = 'truncate `'.TCacheManager::GetCacheInfoTable().'`';
            $cacheDatabaseConnection->query($query);
            // now delete contents of cache dir
            TTools::DelDir(PATH_CACHE, true);
            mkdir(PATH_CACHE);
        } else {
            $query = 'UPDATE `'.TCacheManager::GetCacheTable().'`
                   SET `marked_for_delete` = 1,
                       `mark_for_delete_timestamp` = '.time().",
                       `delete_context` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->GetDeleteContext())."'
                 WHERE `marked_for_delete` = 0
                   AND `created_timestamp` < '".MySqlLegacySupport::getInstance()->real_escape_string($dTime)."'
               ";
            $cacheDatabaseConnection->query($query);
        }
    }

    protected function getCacheDatabaseConnection()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_cms_cache.cache_database_connection');
    }
}
