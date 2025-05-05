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

use ChameleonSystem\MediaManager\Interfaces\SortColumnInterface;

class SortColumnCollection
{
    /**
     * @var SortColumnInterface[]
     */
    private $sortColumns = [];

    /**
     * Add a sort column, there can be only one per system name.
     *
     * @return void
     */
    public function addColumn(SortColumnInterface $sortColumn)
    {
        $this->sortColumns[$sortColumn->getSystemName()] = $sortColumn;
    }

    /**
     * @return SortColumnInterface[]
     */
    public function getSortColumns()
    {
        return $this->sortColumns;
    }

    /**
     * @param string $systemName
     *
     * @return SortColumnInterface|null
     */
    public function getSortColumnBySystemName($systemName)
    {
        return isset($this->sortColumns[$systemName]) ? $this->sortColumns[$systemName] : null;
    }
}
