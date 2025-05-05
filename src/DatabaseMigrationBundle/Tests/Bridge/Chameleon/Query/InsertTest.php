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
use ChameleonSystem\DatabaseMigrationBundle\Bridge\Chameleon\Query\Insert;
use Doctrine\Common\Collections\Expr\Comparison;

class InsertTest extends AbstractQueryTestCase
{
    /**
     * @var Insert
     */
    private $insert;

    /**
     * @test
     *
     * @dataProvider getData
     */
    public function itShouldGetQueryParams(MigrationQueryData $migrationQueryData, $expectedQuery, array $expectedQueryParams)
    {
        $this->givenDependencies();
        $this->givenAnInserter();
        $this->whenICallGetQuery($migrationQueryData);
        $this->thenIShouldGetQueryAndQueryParams($expectedQuery, $expectedQueryParams);
    }

    private function givenAnInserter()
    {
        $this->insert = new Insert($this->databaseConnection->reveal(), $this->dataAccess->reveal());
    }

    private function whenICallGetQuery(MigrationQueryData $migrationQueryData)
    {
        list($this->actualQuery, $this->actualQueryParams) = $this->insert->getQuery($migrationQueryData);
    }

    /**
     * @return array
     */
    public function getData()
    {
        return [
            [
                $this->createParam('foo_table', 'en'),
                'INSERT INTO `foo_table`',
                [
                ],
            ],
            [
                $this->createParam('foo_table', 'en')
                    ->setFields([
                        'bar' => 'baz',
                    ]),
                'INSERT INTO `foo_table` SET `bar` = ?',
                [
                    'baz',
                ],
            ],
            [
                $this->createParam('foo_table', 'en')
                    ->setFields([
                        'foo' => 'bar',
                        'bar' => 'baz',
                    ])
                    ->setWhereEquals([
                        'stringId' => 'xyz',
                        'suchField' => 'veryValue',
                    ]),
                'INSERT INTO `foo_table` SET `foo` = ?, `bar` = ?',
                [
                    'bar',
                    'baz',
                ],
            ],
            [
                $this->createParam('foo_table', 'de')
                    ->setFields([
                        'translatedField' => 'baz',
                    ]),
                'INSERT INTO `foo_table` SET `translatedField__de` = ?',
                [
                    'baz',
                ],
            ],
            [
                $this->createParam('foo_table', 'de')
                    ->setFields([
                        'foo' => 'bar',
                        'translatedField' => 'baz',
                    ])
                    ->setWhereExpressions([
                        new Comparison('stringId', '=', 'xyz'),
                        new Comparison('suchField', '>', 'veryValue'),
                    ]),
                'INSERT INTO `foo_table` SET `foo` = ?, `translatedField__de` = ?',
                [
                    'bar',
                    'baz',
                ],
            ],
        ];
    }
}
