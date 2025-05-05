<?php

declare(strict_types=1);
require_once __DIR__.'/mocks/EntityListMock.php';

use ChameleonSystem\core\DatabaseAccessLayer\EntityList;
use ChameleonSystem\core\DatabaseAccessLayer\EntityListInterface;
use ChameleonSystem\core\DatabaseAccessLayer\QueryModifierOrderByInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Statement;
use mocks\EntityListMock;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TCMSRecordListTest extends TestCase
{
    /** @var EntityListInterface */
    public static $entityList;

    /** @var MockObject<QueryModifierOrderByInterface> */
    public static $queryModifier;

    /** @var MockObject<Connection> */
    private $connection;

    public function setUp(): void
    {
        parent::setUp();
        $this->connection = $this->createMock(Connection::class);
    }

    public function testIterates(): void
    {
        self::$entityList = new EntityListMock([1, 2, 3]);
        $list = $this->mockRecordList();

        // Iterating multiple times checks if `rewind` behaves correctly
        $items = [];
        foreach ($list as $item) {
            $items[] = $item;
        }
        foreach ($list as $item) {
            $items[] = $item;
        }

        $this->assertEquals([1, 2, 3, 1, 2, 3], $items);
    }

    public function testIteratesInCombinationWithEntityList(): void
    {
        self::$queryModifier = $this->createMock(QueryModifierOrderByInterface::class);
        self::$queryModifier->method('getQueryWithOrderBy')->willReturnArgument(0);
        self::$queryModifier->method('getQueryWithoutOrderBy')->willReturnArgument(0);

        self::$entityList = new class($this->connection, 'SELECT * FROM foo') extends EntityList {
            protected function getQueryModifierOrderByService(): QueryModifierOrderByInterface
            {
                return TCMSRecordListTest::$queryModifier;
            }
        };

        $list = $this->mockRecordList();

        // The data that should be iterated is supplied by mocking the database connection
        // inside the entitylist itself.
        $result = $this->createMock(Statement::class);
        $result->method('fetch')->willReturnOnConsecutiveCalls(1, 2, 3, false);
        $this->connection
            ->expects($this->once())
            ->method('executeQuery')
            ->willReturn($result);
        $this->connection
            ->method('fetchArray')
            ->with($this->matchesRegularExpression('/COUNT\(\*\)/'))
            ->willReturn([3]);

        // Iterating multiple times checks if `rewind` behaves correctly
        $items = [];
        foreach ($list as $item) {
            $items[] = $item;
        }
        foreach ($list as $item) {
            $items[] = $item;
        }

        $this->assertEquals([1, 2, 3, 1, 2, 3], $items);
    }

    /**
     * Assumes, that `entityList` was set before.
     */
    private function mockRecordList(): TCMSRecordList
    {
        return new class() extends TCMSRecordList {
            // Mocks away container parameter
            protected $estimationLowerLimit = 0;

            // Swaps out the iterator to a mock
            protected function getEntityList()
            {
                return TCMSRecordListTest::$entityList;
            }

            // Does not create a new Tdb for the data returned from the iterator
            // but returns the data 1:1
            protected function _NewElement($aData)
            {
                return $aData;
            }
        };
    }
}
