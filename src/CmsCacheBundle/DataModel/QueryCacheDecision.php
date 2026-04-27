<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CmsCacheBundle\DataModel;

class QueryCacheDecision
{
    public const string REASON_OK = 'ok';
    public const string REASON_SQL_NOT_STRING = 'sql_not_string';
    public const string REASON_SQL_EMPTY = 'sql_empty';
    public const string REASON_NOT_SELECT = 'not_select';
    public const string REASON_DATETIME_PARAMETER = 'datetime_parameter';
    public const string REASON_NO_TABLES_DETECTED = 'no_tables_detected';
    public const string REASON_CONTAINS_UNCACHEABLE_TABLES = 'contains_uncacheable_tables';

    /**
     * @param array<int, string> $tables
     * @param array<int, string> $uncacheableTables
     */
    public function __construct(
        private readonly bool $cacheable,
        private readonly string $reason,
        private readonly array $tables = [],
        private readonly array $uncacheableTables = []
    ) {
    }

    /**
     * @param array<int, string> $tables
     */
    public static function cacheable(array $tables): self
    {
        return new self(true, self::REASON_OK, $tables);
    }

    public static function sqlNotString(): self
    {
        return new self(false, self::REASON_SQL_NOT_STRING);
    }

    public static function sqlEmpty(): self
    {
        return new self(false, self::REASON_SQL_EMPTY);
    }

    public static function notSelect(): self
    {
        return new self(false, self::REASON_NOT_SELECT);
    }

    public static function dateTimeParameter(): self
    {
        return new self(false, self::REASON_DATETIME_PARAMETER);
    }

    public static function noTablesDetected(): self
    {
        return new self(false, self::REASON_NO_TABLES_DETECTED);
    }

    /**
     * @param array<int, string> $tables
     * @param array<int, string> $uncacheableTables
     */
    public static function containsUncacheableTables(array $tables, array $uncacheableTables): self
    {
        return new self(false, self::REASON_CONTAINS_UNCACHEABLE_TABLES, $tables, $uncacheableTables);
    }

    public function isCacheable(): bool
    {
        return $this->cacheable;
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    /**
     * @return array<int, string>
     */
    public function getTables(): array
    {
        return $this->tables;
    }

    /**
     * @return array<int, string>
     */
    public function getUncacheableTables(): array
    {
        return $this->uncacheableTables;
    }
}
