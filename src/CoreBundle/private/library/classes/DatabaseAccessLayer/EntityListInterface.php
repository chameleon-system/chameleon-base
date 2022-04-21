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
    public function getCurrentPosition();

    /**
     * change internal index to the last element.
     *
     * @return void
     */
    public function end();

    /**
     * set a new query to use for the result set. changing the query will also change the list state (drop cached results, etc).
     *
     * @param string $query
     * @return void
     */
    public function setQuery($query);

    /**
     * position internal pointer the the previous element.
     *
     * @return void
     */
    public function previous();

    /**
     * return an estimate of the number of results matching the query.
     *
     * @return int
     */
    public function estimateCount();

    /**
     * change page size. -1 = no limit.
     *
     * @param int $pageSize
     *
     * @return $this
     */
    public function setPageSize($pageSize);

    /**
     * @param int $currentPage
     *
     * @return $this
     */
    public function setCurrentPage($currentPage);

    /**
     * limit results to - pass null to remove the restriction.
     *
     * @param int $maxNumberOfResults
     * @return void
     */
    public function setMaxAllowedResults($maxNumberOfResults);
}
