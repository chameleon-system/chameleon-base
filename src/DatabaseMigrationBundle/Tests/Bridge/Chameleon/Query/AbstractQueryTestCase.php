<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\DatabaseMigrationBundle\Tests\Bridge\Chameleon\Query;

use ChameleonSystem\DatabaseMigration\Query\MigrationQueryData;
use ChameleonSystem\DatabaseMigrationBundle\Bridge\Chameleon\DataAccess\AbstractQueryDataAccessInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class AbstractQueryTestCase extends TestCase
{
    use ProphecyTrait;

    /**
     * @var ObjectProphecy
     */
    protected $databaseConnection;

    /**
     * @var AbstractQueryDataAccessInterface|ObjectProphecy
     */
    protected $dataAccess;

    /**
     * @var string
     */
    protected $actualQuery;

    /**
     * @var array
     */
    protected $actualQueryParams;

    protected function tearDown(): void
    {
        $this->getProphet()->checkPredictions();
    }

    protected function givenDependencies()
    {
        $this->databaseConnection = $this->prophesize('Doctrine\DBAL\Connection');
        $this->mockQuoteIdentifier();
        $this->mockQuote();

        $this->dataAccess = $this->prophesize('ChameleonSystem\DatabaseMigrationBundle\Bridge\Chameleon\DataAccess\AbstractQueryDataAccessInterface');
        $this->dataAccess->getBaseLanguageIso()->willReturn('en');
        $this->dataAccess->getTranslatedFieldsForTable('foo_table')->willReturn(['translatedField']);
    }

    protected function mockQuote()
    {
        $this->databaseConnection->quote(Argument::any())->will(function (array $arguments) {
            return "'{$arguments[0]}'";
        });
    }

    protected function mockQuoteIdentifier()
    {
        $this->databaseConnection->quoteIdentifier(Argument::any())->will(function (array $arguments) {
            return "`{$arguments[0]}`";
        });
    }

    protected function thenIShouldGetQueryAndQueryParams($expectedQuery, $expectedQueryParams)
    {
        $this->assertEquals($expectedQuery, $this->actualQuery);
        $this->assertEquals($expectedQueryParams, $this->actualQueryParams);
    }

    /**
     * Creates a MigrationQueryData object, avoiding the PHP 5.3 limitation of not being able to call "fluent constructors".
     *
     * @return MigrationQueryData
     */
    protected function createParam($tableName, $language)
    {
        return new MigrationQueryData($tableName, $language);
    }
}
