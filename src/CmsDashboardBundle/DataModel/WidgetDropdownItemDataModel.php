<?php
/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CmsDashboardBundle\DataModel;

class WidgetDropdownItemDataModel
{
    private string $id;
    private string $title;
    private string $url;
    private array $dataAttributes = [];

    public function __construct(string $id, string $title, string $url)
    {
        $this->id = $id;
        $this->title = $title;
        $this->url = $url;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getDataAttributes(): array
    {
        return $this->dataAttributes;
    }

    public function setDataAttributes(array $dataAttributes): void
    {
        $this->dataAttributes = $dataAttributes;
    }

    public function addDataAttribute(string $dataAttributeKey, string $dataAttributeValue): void
    {
        $this->dataAttributes[$dataAttributeKey] = $dataAttributeValue;
    }
}
