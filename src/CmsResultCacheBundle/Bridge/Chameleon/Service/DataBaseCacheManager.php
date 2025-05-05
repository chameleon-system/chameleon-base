<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CmsResultCacheBundle\Bridge\Chameleon\Service;

use Doctrine\DBAL\Connection;

class DataBaseCacheManager
{
    public function __construct(private readonly Connection $dbConnection)
    {
    }

    public function get(string $owner, string $key, bool $ignoreExpireTime = false): mixed
    {
        $cacheValue = false;

        $oResultCache = \TdbPkgCmsResultCache::GetNewInstance();
        if ($oResultCache->LoadFromFields(['owner_hash' => sha1($owner), 'hash' => sha1($key)])) {
            $cacheValue = unserialize(base64_decode($oResultCache->fieldData));

            if (false === $ignoreExpireTime) {
                $expireTimeStamp = strtotime($oResultCache->fieldDateExpireAfter);
                if (time() > $expireTimeStamp) {
                    $cacheValue = false;
                }
            }
        }

        return $cacheValue;
    }

    public function exists(string $owner, string $key): bool
    {
        $resultCache = \TdbPkgCmsResultCache::GetNewInstance();

        return false !== $resultCache->LoadFromFields(['owner_hash' => sha1($owner), 'hash' => sha1($key)]);
    }

    /**
     * @param string $key
     */
    public function set(string $owner, $key, $value, ?int $expireTimestamp, bool $allowGarbageCollection = true): void
    {
        $cacheEntryData = [];
        $cacheEntryData['owner_hash'] = sha1($owner);
        $cacheEntryData['hash'] = sha1($key);
        $cacheEntryData['data'] = base64_encode(serialize($value));
        if (null !== $expireTimestamp) {
            $cacheEntryData['date_expire_after'] = date('Y-m-d H:i:s', $expireTimestamp);
        }
        $cacheEntryData['garbage_collect_when_expired'] = $allowGarbageCollection ? '1' : '0';
        $resultCache = \TdbPkgCmsResultCache::GetNewInstance();
        $mergedCacheEntryData = $cacheEntryData;
        if (true === $resultCache->LoadFromFields(
            ['owner_hash' => $cacheEntryData['owner_hash'], 'hash' => $cacheEntryData['hash']]
        )
        ) {
            $mergedCacheEntryData = $resultCache->sqlData;
            foreach ($cacheEntryData as $existingCacheDataKey => $existingCacheDataValue) {
                $mergedCacheEntryData[$existingCacheDataKey] = $existingCacheDataValue;
            }
        } else {
            $mergedCacheEntryData['date_created'] = date('Y-m-d H:i:s');
        }
        $resultCache->LoadFromRow($mergedCacheEntryData);
        $resultCache->AllowEditByAll(true);
        $resultCache->Save();
        $resultCache->AllowEditByAll(false);
    }

    /**
     * Deletes expired cache entries.
     */
    public function garbageCollector(): void
    {
        $query = "DELETE FROM `pkg_cms_result_cache`
                          WHERE `pkg_cms_result_cache`.`garbage_collect_when_expired` = '1'
                            AND `pkg_cms_result_cache`.`date_expire_after` <  NOW()";
        $this->dbConnection->executeQuery($query);
    }
}
