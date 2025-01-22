<?php

namespace ChameleonSystem\BreadcrumbBundle\Library\DataModel;

/**
 * @template-implements \Iterator<int, string>
 */
final class BreadcrumbDataModel implements \Iterator, \Countable
{
    /**
     * @var BreadcrumbItemDataModel[]
     */
    private array $items = [];

    public function add(BreadcrumbItemDataModel $breadcrumbItemDataModel): void
    {
        array_unshift($this->items, $breadcrumbItemDataModel);
    }

    public function current(): bool|BreadcrumbItemDataModel
    {
        return current($this->items);
    }

    public function next(): void
    {
        next($this->items);
    }

    public function key(): string
    {
        return key($this->items);
    }

    public function valid(): bool
    {
        return false !== $this->current();
    }

    public function rewind(): void
    {
        reset($this->items);
    }

    public function count(): int
    {
        return count($this->items);
    }
}
