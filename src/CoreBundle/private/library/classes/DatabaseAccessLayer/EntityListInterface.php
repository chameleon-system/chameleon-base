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
 *
 * @implements \SeekableIterator<int, T>
 */
interface EntityListInterface extends \SeekableIterator, \Countable
{
    public function getCurrentPosition(): int;

    /**
     * change internal index to the last element.
     */
    public function end(): void;

    /**
     * set a new query to use for the result set. changing the query will also change the list state (drop cached results, etc).
     */
    public function setQuery(string $query): void;

    /**
     * position internal pointer the the previous element.
     */
    public function previous(): void;

    /**
     * return an estimate of the number of results matching the query.
     */
    public function estimateCount(): int;

    /**
     * change page size. -1 = no limit.
     *
     * @return $this
     */
    public function setPageSize(int $pageSize): self;

    /**
     * @return $this
     */
    public function setCurrentPage(int $currentPage): self;

    /**
     * limit results to - pass null to remove the restriction.
     */
    public function setMaxAllowedResults(int $maxNumberOfResults): void;
}
