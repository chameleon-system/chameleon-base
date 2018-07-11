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
     * @var null|MediaTreeNodeDataModel
     */
    private $mediaTreeNode;

    /**
     * @var bool
     */
    private $subTreeIncluded = true;

    /**
     * @var null|string
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
     * @var null|SortColumnInterface
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
     */
    public function setSubTreeIncluded($subTreeIncluded)
    {
        $this->subTreeIncluded = $subTreeIncluded;
    }

    /**
     * @return null|string
     */
    public function getSearchTerm()
    {
        return $this->searchTerm;
    }

    /**
     * @param null|string $searchTerm
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
     */
    public function setPageSize($pageSize)
    {
        $this->pageSize = $pageSize;
    }

    /**
     * @return string|null
     */
    public function getSortColumn()
    {
        return $this->sortColumn;
    }

    /**
     * @param string|null $sortColumn
     */
    public function setSortColumn($sortColumn)
    {
        $this->sortColumn = $sortColumn;
    }
}
