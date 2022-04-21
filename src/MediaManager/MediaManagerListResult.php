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

use ChameleonSystem\MediaManager\DataModel\MediaItemDataModel;

class MediaManagerListResult
{
    /**
     * @var MediaItemDataModel[]
     */
    private $items;

    /**
     * @var int
     */
    private $numberOfPages;

    /**
     * @var int
     */
    private $numberOfItems = 0;

    /**
     * @param MediaItemDataModel[] $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @return MediaItemDataModel[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return int
     */
    public function getNumberOfPages()
    {
        return $this->numberOfPages;
    }

    /**
     * @param int $numberOfPages
     *
     * @return void
     */
    public function setNumberOfPages($numberOfPages)
    {
        $this->numberOfPages = $numberOfPages;
    }

    /**
     * @return int
     */
    public function getNumberOfItems()
    {
        return $this->numberOfItems;
    }

    /**
     * @param int $numberOfItems
     *
     * @return void
     */
    public function setNumberOfItems($numberOfItems)
    {
        $this->numberOfItems = $numberOfItems;
    }
}
