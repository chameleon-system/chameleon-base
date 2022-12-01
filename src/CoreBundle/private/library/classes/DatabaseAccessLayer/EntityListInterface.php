<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\core\DatabaseAccessLayer;

/**
 * @template T
 * @implements \SeekableIterator<int, T>
 */
interface EntityListInterface extends \SeekableIterator, \Countable
{
    /**
     * @return int
     */
    public function getCurrentPosition(): int;

    /**
     * change internal index to the last element.
     *
     * @return void
     */
    public function end(): void;

    /**
     * set a new query to use for the result set. changing the query will also change the list state (drop cached results, etc).
     *
     * @param string $query
     * @return void
     */
    public function setQuery(string $query): void;

    /**
     * position internal pointer the the previous element.
     *
     * @return void
     */
    public function previous(): void;

    /**
     * return an estimate of the number of results matching the query.
     *
     * @return int
     */
    public function estimateCount(): int;

    /**
     * change page size. -1 = no limit.
     *
     * @param int $pageSize
     *
     * @return $this
     */
    public function setPageSize(int $pageSize): self;

    /**
     * @param int $currentPage
     *
     * @return $this
     */
    public function setCurrentPage(int $currentPage): self;

    /**
     * limit results to - pass null to remove the restriction.
     *
     * @param int $maxNumberOfResults
     * @return void
     */
    public function setMaxAllowedResults(int $maxNumberOfResults):void;
}
