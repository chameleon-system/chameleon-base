<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\MediaManager;

use ChameleonSystem\MediaManager\DataModel\MediaTreeNodeDataModel;
use ChameleonSystem\MediaManager\Interfaces\SortColumnInterface;

class MediaManagerListRequest
{
    /**
     * @var MediaTreeNodeDataModel|null
     */
    private $mediaTreeNode;

    /**
     * @var bool
     */
    private $subTreeIncluded = true;

    /**
     * @var string|null
     */
    private $searchTerm;

    /**
     * @var int
     */
    private $pageNumber = 0;

    /**
     * @var int
     */
    private $pageSize = -1;

    /**
     * @var SortColumnInterface|null
     */
    private $sortColumn;

    /**
     * @return MediaTreeNodeDataModel|null
     */
    public function getMediaTreeNode()
    {
        return $this->mediaTreeNode;
    }

    /**
     * @param MediaTreeNodeDataModel|null $mediaTreeNode
     *
     * @return void
     */
    public function setMediaTreeNode($mediaTreeNode)
    {
        $this->mediaTreeNode = $mediaTreeNode;
    }

    /**
     * @return bool
     */
    public function isSubTreeIncluded()
    {
        return $this->subTreeIncluded;
    }

    /**
     * @param bool $subTreeIncluded
     *
     * @return void
     */
    public function setSubTreeIncluded($subTreeIncluded)
    {
        $this->subTreeIncluded = $subTreeIncluded;
    }

    /**
     * @return string|null
     */
    public function getSearchTerm()
    {
        return $this->searchTerm;
    }

    /**
     * @param string|null $searchTerm
     *
     * @return void
     */
    public function setSearchTerm($searchTerm)
    {
        $this->searchTerm = $searchTerm;
    }

    /**
     * @return int
     */
    public function getPageNumber()
    {
        return $this->pageNumber;
    }

    /**
     * @param int $pageNumber
     *
     * @return void
     */
    public function setPageNumber($pageNumber)
    {
        $this->pageNumber = $pageNumber;
    }

    /**
     * @return int
     */
    public function getPageSize()
    {
        return $this->pageSize;
    }

    /**
     * @param int $pageSize
     *
     * @return void
     */
    public function setPageSize($pageSize)
    {
        $this->pageSize = $pageSize;
    }

    /**
     * @FIXME `$this->sortColumn` is annotated as `SortColumnInterface|null` and `MediaItemDataAccess::getOrderBy()` uses it
     *         as such. But `MediaManagerListRequestFactory` fills it with `string|null` from `MediaManagerListState` which
     *         in turn is set to a string directly from the request in `MediaManagerListStateFromRequestService`.
     *
     * @psalm-suppress InvalidReturnType, InvalidReturnStatement
     *
     * @return string|null
     */
    public function getSortColumn()
    {
        return $this->sortColumn;
    }

    /**
     * @FIXME `$this->sortColumn` is annotated as `SortColumnInterface|null` and `MediaItemDataAccess::getOrderBy()` uses it
     *         as such. But `MediaManagerListRequestFactory` fills it with `string|null` from `MediaManagerListState` which
     *         in turn is set to a string directly from the request in `MediaManagerListStateFromRequestService`.
     *
     * @psalm-suppress InvalidPropertyAssignmentValue
     *
     * @param string|null $sortColumn
     *
     * @return void
     */
    public function setSortColumn($sortColumn)
    {
        $this->sortColumn = $sortColumn;
    }
}
