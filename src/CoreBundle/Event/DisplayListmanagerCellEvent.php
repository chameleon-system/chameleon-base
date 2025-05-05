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
    private \TGroupTableField $tableCell;
    private array $rowData;
    private bool $isHeader;
    private array $attributes = [];
    private string $onclickEvent = '';
    private array $cssClasses = [];
    private string $cellValue = '';
    private string $cellValueWithDetailLink = '';

    public function __construct(\TGroupTableField $tableCell, array $rowData, bool $isHeader)
    {
        $this->tableCell = $tableCell;
        $this->rowData = $rowData;
        $this->isHeader = $isHeader;
    }

    public function getTableCell(): \TGroupTableField
    {
        return $this->tableCell;
    }

    public function getRowData(): array
    {
        return $this->rowData;
    }

    public function isHeader(): bool
    {
        return $this->isHeader;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    public function getOnclickEvent(): string
    {
        return $this->onclickEvent;
    }

    public function setOnclickEvent(string $onclickEvent): void
    {
        $this->onclickEvent = $onclickEvent;
    }

    public function getCssClasses(): array
    {
        return $this->cssClasses;
    }

    public function setCssClasses(array $cssClasses): void
    {
        $this->cssClasses = $cssClasses;
    }

    public function getCellValue(): string
    {
        return $this->cellValue;
    }

    public function setCellValue(string $cellValue): void
    {
        $this->cellValue = $cellValue;
    }

    public function getCellValueWithDetailLink(): string
    {
        return $this->cellValueWithDetailLink;
    }

    public function setCellValueWithDetailLink(string $cellValueWithDetailLink): void
    {
        $this->cellValueWithDetailLink = $cellValueWithDetailLink;
    }
}
