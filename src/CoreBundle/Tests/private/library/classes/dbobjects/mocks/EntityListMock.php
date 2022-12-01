<?php declare(strict_types=1);

namespace mocks;

use ChameleonSystem\core\DatabaseAccessLayer\EntityListInterface;
use function count;

class EntityListMock implements EntityListInterface
{

    public array $items;
    public int $pointer = 0;

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    public function next()
    {
        $this->pointer++;
    }

    public function valid()
    {
        return $this->pointer < count($this->items);
    }

    public function rewind()
    {
        $this->pointer = 0;
    }

    public function count()
    {
        return count($this->items);
    }

    public function getCurrentPosition(): int
    {
        return $this->pointer;
    }

    public function end(): void
    {
        $this->pointer = count($this->items) - 1;
    }

    public function setQuery(string $query): void
    {
        // Does nothing
    }

    public function previous(): void
    {
        $this->pointer--;
    }

    public function estimateCount(): int
    {
        return count($this->items);
    }

    public function setPageSize(int $pageSize): self
    {
        // Does nothing
    }

    public function setCurrentPage(int $currentPage): self
    {
        // Does nothing
    }

    public function setMaxAllowedResults(int $maxNumberOfResults):void
    {
        // Does nothing
    }

    public function seek(int $offset)
    {
        $this->pointer = $offset;
    }

    public function current()
    {
        return $this->items[$this->pointer];
    }

    public function key()
    {
        return $this->pointer;
    }
}
