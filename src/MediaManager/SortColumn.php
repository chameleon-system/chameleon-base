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

class SortColumn implements SortColumnInterface
{
    /**
     * @var string
     */
    private $columnName;

    /**
     * @var string
     */
    private $systemName;

    /**
     * @var string
     */
    private $sortDirection;

    /**
     * @param string $columnName
     * @param string $systemName
     * @param string $sortDirection
     */
    public function __construct($columnName, $systemName, $sortDirection)
    {
        $this->columnName = $columnName;
        $this->systemName = $systemName;
        $this->sortDirection = $sortDirection;
    }

    /**
     * {@inheritdoc}
     */
    public function getSortDirection()
    {
        return $this->sortDirection;
    }

    /**
     * {@inheritdoc}
     */
    public function getColumnName()
    {
        return $this->columnName;
    }

    /**
     * {@inheritdoc}
     */
    public function getSystemName()
    {
        return $this->systemName;
    }
}
