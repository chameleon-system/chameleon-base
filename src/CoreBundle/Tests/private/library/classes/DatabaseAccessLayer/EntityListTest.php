<?php

declare(strict_types=1);

use ChameleonSystem\core\DatabaseAccessLayer\EntityList;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Statement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EntityListTest extends TestCase
{
    /** @var EntityList */
    private $subject;

    /** @var MockObject<Connection> */
    private $connection;

    public function setUp(): void
    {
        $this->connection = $this->createMock(Connection::class);
        $this->subject = new EntityList(
            $this->connection, 'SELECT * FROM foo'
        );
    }

    public function testEntityList(): void
    {
        $result = $this->createMock(Statement::class);
        $result->method('fetch')->willReturnOnConsecutiveCalls(1, 2, 3, false);
        $this->connection
            ->expects($this->once())
            ->method('executeQuery')
            ->willReturn($result);

        // Iterating multiple times checks if `rewind` behaves correctly
        $items = [];
        foreach ($this->subject as $item) {
            $items[] = $item;
        }
        foreach ($this->subject as $item) {
            $items[] = $item;
        }

        $this->assertEquals([1, 2, 3, 1, 2, 3], $items);
    }
}
