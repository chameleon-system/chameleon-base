<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CmsCacheBundle\Doctrine;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver;
use ChameleonSystem\CoreBundle\ServiceLocator;
use Psr\Log\LoggerInterface;
use esono\pkgCmsCache\CacheInterface;

class LoggingConnectionWrapper extends Connection
{
    private const array CACHABLE_TABLES = [
        'cms_language',
        'cms_portal',
        'cms_locals',
        'data_extranet',
        'esono_ab_test',
        'shop',
        'pkg_shop_currency',
        'cms_tpl_page',
        'cms_master_pagedef',
        'pkg_cms_theme',
        'cms_field_conf',
        'cms_field_type',
        'cms_master_pagedef_spot',
        'cms_master_pagedef_spot_parameter',
        'cms_master_pagedef_spot_access',
        'cms_tpl_page_cms_master_pagedef_spot',
        'cms_portal_system_page',
        'cms_tree',
        'shop_attribute',
        'shop_module_article_list',
        'shop_module_article_list_filter',
        'shop_module_articlelist_orderby',

        //addition
        'shop_category',
        'shop_article_image_size',
        'shop_stock_message_trigger',
        'schafferer_notification_layover',
        'pkg_cms_theme_block_layout',
        'pkg_cms_theme_block',
        'pkg_shop_listfilter_item',
        'cms_config_imagemagick',
        'pkg_cms_text_block',
        'cms_portal_domains',
        'shop_module_articlelist_orderby',
        'shop_module_article_list_shop_article_group_mlt',
        'shop_module_article_list_schafferer_product_event_teaser_mlt',
        'schafferer_notification_layover_cms_portal_domains_mlt',
        'schafferer_circuit_breaker',
        'cms_master_pagedef_spot',
        'pkg_external_tracker'
    ];

    private const string DEBUG_TABLE = 'cms_tree_node';

    private readonly LoggerInterface $logger;
    public function __construct(
        array $params,
        Driver $driver,
        ?Configuration $config = null,
        ?EventManager $eventManager = null
    ){
        parent::__construct($params, $driver, $config, $eventManager);
        //Unfortunatly we can't initilize it with the logger, as DBAL uses a factory
        $this->logger = $this->getLogger();
    }
    /**
     * @param mixed $sql
     * @param array<string, mixed>|array<int, mixed> $params
     * @param array<string, mixed>|array<int, mixed> $types
     */
    public function executeQuery($sql, array $params = [], $types = [], ?QueryCacheProfile $qcp = null)
    {
        return parent::executeQuery($sql, $params, $types, $qcp);
    }

    /**
     * @param mixed $sql
     */
    public function prepare($sql)
    {
        if(false !== strpos($sql, 'shop_module_article_list_filter')){
            $debug = true;
        }
        $this->logCall('prepare', $sql);

        return parent::prepare($sql);
    }

    /**
     * @param string $query
     * @param array<string, mixed>|array<int, mixed> $params
     * @param array<string, mixed>|array<int, mixed> $types
     *
     * @return array<string, mixed>|false
     */
    public function fetchAssociative(string $query, array $params = [], array $types = [])
    {
        $cachableQuery = $this->isCachableQuery($query);
        if (true === $cachableQuery) {
            $cachedResult = $this->getFromCache($query, $params, $types);
            if (null !== $cachedResult) {
                return $cachedResult;
            }
        }

        $result = parent::fetchAssociative($query, $params, $types);

        if (true === $cachableQuery) {
            $this->saveInCache($result, $query, $params, $types);
        }

        return $result;
    }

    /**
     * @param string $query
     * @param array<string, mixed>|array<int, mixed> $params
     * @param array<string, mixed>|array<int, mixed> $types
     *
     * @return array<int, mixed>|false
     */
    public function fetchNumeric(string $query, array $params = [], array $types = [])
    {
        $cachableQuery = $this->isCachableQuery($query);
        if (true === $cachableQuery) {
            $cachedResult = $this->getFromCache($query, $params, $types);
            if (null !== $cachedResult) {
                return $cachedResult;
            }
        }

        $result = parent::fetchNumeric($query, $params, $types);

        if (true === $cachableQuery) {
            $this->saveInCache($result, $query, $params, $types);
        }

        return $result;
    }

    /**
     * @param string $query
     * @param array<string, mixed>|array<int, mixed> $params
     * @param array<string, mixed>|array<int, mixed> $types
     *
     * @return mixed|false
     */
    public function fetchOne(string $query, array $params = [], array $types = [])
    {
        $cachableQuery = $this->isCachableQuery($query);
        if (true === $cachableQuery) {
            $cachedResult = $this->getFromCache($query, $params, $types);
            if (null !== $cachedResult) {
                return $cachedResult;
            }
        }

        $result = parent::fetchOne($query, $params, $types);

        if (true === $cachableQuery) {
            $this->saveInCache($result, $query, $params, $types);
        }

        return $result;
    }

    /**
     * @param string $query
     * @param array<string, mixed>|array<int, mixed> $params
     * @param array<string, mixed>|array<int, mixed> $types
     *
     * @return array<int, array<string, mixed>>
     */
    public function fetchAllAssociative(string $query, array $params = [], array $types = []): array
    {
        $cachableQuery = $this->isCachableQuery($query);
        if (true === $cachableQuery) {
            $cachedResult = $this->getFromCache($query, $params, $types);
            if (null !== $cachedResult) {
                return $cachedResult;
            }
        }

        $result = parent::fetchAllAssociative($query, $params, $types);

        if (true === $cachableQuery) {
            $this->saveInCache($result, $query, $params, $types);
        }

        return $result;
    }

    /**
     * @param mixed $sql
     * @param array<string, mixed>|array<int, mixed> $params
     * @param array<string, mixed>|array<int, mixed> $types
     *
     * @return int|string
     */
    public function executeStatement($sql, array $params = [], array $types = [])
    {
        if(false !== strpos($sql, self::DEBUG_TABLE)){
            $debug = true;
        }
        $this->logCall('executeStatement', $sql, $params);

        return parent::executeStatement($sql, $params, $types);
    }

    /**
     * @param mixed ...$args
     */
    public function query()
    {
        $args = func_get_args();
        $this->logCall('query', $args[0] ?? '');

        return parent::query(...$args);
    }

    /**
     * @param mixed $sql
     *
     * @return int|string
     */
    public function exec($sql)
    {
        if(false !== strpos($sql, self::DEBUG_TABLE)){
            $debug = true;
        }
        $this->logCall('exec', $sql);

        return parent::exec($sql);
    }

    /**
     * @param mixed $sql
     * @param array<string, mixed>|array<int, mixed> $params
     */
    private function logCall(string $method, $sql, array $params = []): void
    {
        $sqlString = is_string($sql) ? $sql : '[non-string-sql]';
        $sqlString = preg_replace('/\s+/', ' ', trim($sqlString));
        if (null === $sqlString || '' === $sqlString) {
            $sqlString = '[empty-sql]';
        }

        $preview = substr($sqlString, 0, 300);
        $message = sprintf(
            '[dbal-wrapper-poc] method=%s params=%d sql="%s"%s',
            $method,
            count($params),
            $preview,
            strlen($sqlString) > strlen($preview) ? '...' : ''
        );

        $this->logger->info($message);
    }

    private function getLogger(): LoggerInterface{
        return ServiceLocator::get('logger');
    }

    private function isCachableQuery($sql): bool
    {
        if (false === is_string($sql)) {
            return false;
        }

        $normalizedSql = trim(preg_replace('/\s+/', ' ', $sql) ?? '');
        if ('' === $normalizedSql) {
            return false;
        }

        if (1 !== preg_match('/^SELECT\b/i', $normalizedSql)) {
            return false;
        }

        preg_match_all('/\b(?:FROM|JOIN)\s+`?([a-zA-Z0-9_]+)`?/i', $normalizedSql, $tableMatches);
        $tables = array_values(array_unique($tableMatches[1] ?? []));

        if (0 === count($tables)) {
            return false;
        }

        foreach ($tables as $table) {
            if (false === in_array($table, self::CACHABLE_TABLES, true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param mixed $sql
     * @param array<string, mixed>|array<int, mixed> $params
     * @param array<string, mixed>|array<int, mixed> $types
     */
    private function getFromCache($sql, array $params = [], array $types = []): mixed
    {
        $cache = $this->getCache();
        if (false === $cache->isActive()) {
            return false;
        }

        $cacheKey = $cache->getKey([
            'owner' => __CLASS__,
            'sql' => $sql,
            'params' => $params,
            'types' => $types,
        ]);

        $cachedQuery = $cache->get($cacheKey);
        if (null === $cachedQuery) {
            return null;
        }

        return $cachedQuery;
    }

    /**
     * @param mixed $queryResult
     * @param mixed $sql
     * @param array<string, mixed>|array<int, mixed> $params
     * @param array<string, mixed>|array<int, mixed> $types
     */
    private function saveInCache($queryResult, $sql, array $params = [], array $types = []): void
    {
        $cache = $this->getCache();
        if (false === $cache->isActive()) {
            return;
        }

        preg_match_all('/\b(?:FROM|JOIN)\s+`?([a-zA-Z0-9_]+)`?/i', (string) $sql, $tableMatches);
        $tables = array_values(array_unique($tableMatches[1] ?? []));
        $triggers = [];
        foreach ($tables as $table) {
            if (true === in_array($table, self::CACHABLE_TABLES, true)) {
                $triggers[] = [
                    'table' => $table,
                    'id' => null,
                ];
            }
        }

        $cacheKey = $cache->getKey([
            'owner' => __CLASS__,
            'sql' => $sql,
            'params' => $params,
            'types' => $types,
        ]);

        $cache->set($cacheKey, $queryResult, $triggers);
    }

    private function getCache(): CacheInterface
    {
        return ServiceLocator::get('chameleon_system_cms_cache.cache');
    }
}
