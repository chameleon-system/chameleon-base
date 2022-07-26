<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Holds information on a single backend table cell. This information can be changed to alter display of the cell.
 */
class DisplayListmanagerCellEvent extends Event
{
    /**
     * @var \TGroupTableField
     */
    private $tableCell;
    /**
     * @var array
     */
    private $rowData;
    /**
     * @var bool
     */
    private $isHeader;
    /**
     * @var array
     */
    private $attributes = [];
    /**
     * @var string
     */
    private $onclickEvent;
    /**
     * @var array
     */
    private $cssClasses = [];
    /**
     * @var string
     */
    private $cellValue = '';

    /**
     * @param \TGroupTableField $tableCell
     * @param array             $rowData
     * @param bool              $isHeader
     */
    public function __construct(\TGroupTableField $tableCell, array $rowData, $isHeader)
    {
        $this->tableCell = $tableCell;
        $this->rowData = $rowData;
        $this->isHeader = $isHeader;
    }

    /**
     * @return \TGroupTableField
     */
    public function getTableCell()
    {
        return $this->tableCell;
    }

    /**
     * @return array
     */
    public function getRowData()
    {
        return $this->rowData;
    }

    /**
     * @return bool
     */
    public function isHeader()
    {
        return $this->isHeader;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     *
     * @return void
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @return string
     */
    public function getOnclickEvent()
    {
        return $this->onclickEvent;
    }

    /**
     * @param string $onclickEvent
     *
     * @return void
     */
    public function setOnclickEvent($onclickEvent)
    {
        $this->onclickEvent = $onclickEvent;
    }

    /**
     * @return array
     */
    public function getCssClasses()
    {
        return $this->cssClasses;
    }

    /**
     * @param array $cssClasses
     *
     * @return void
     */
    public function setCssClasses(array $cssClasses)
    {
        $this->cssClasses = $cssClasses;
    }

    /**
     * @return string
     */
    public function getCellValue()
    {
        return $this->cellValue;
    }

    /**
     * @param string $cellValue
     *
     * @return void
     */
    public function setCellValue($cellValue)
    {
        $this->cellValue = $cellValue;
    }
}
