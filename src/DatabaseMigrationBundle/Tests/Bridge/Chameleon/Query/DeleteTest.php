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

use ChameleonSystem\DatabaseMigration\Constant\QueryConstants;
use ChameleonSystem\DatabaseMigration\Query\MigrationQueryData;
use ChameleonSystem\DatabaseMigrationBundle\Bridge\Chameleon\Query\Delete;
use Doctrine\Common\Collections\Expr\Comparison;

class DeleteTest extends AbstractQueryTestCase
{
    /**
     * @var Delete
     */
    private $delete;

    /**
     * @test
     *
     * @dataProvider getData
     */
    public function itShouldGetQueryParams(MigrationQueryData $migrationQueryData, $expectedQuery, array $expectedQueryParams)
    {
        $this->givenDependencies();
        $this->givenADeleter();
        $this->whenICallGetQuery($migrationQueryData);
        $this->thenIShouldGetQueryAndQueryParams($expectedQuery, $expectedQueryParams);
    }

    private function givenADeleter()
    {
        $this->delete = new Delete($this->databaseConnection->reveal(), $this->dataAccess->reveal());
    }

    private function whenICallGetQuery(MigrationQueryData $migrationQueryData)
    {
        list($this->actualQuery, $this->actualQueryParams) = $this->delete->getQuery($migrationQueryData);
    }

    /**
     * @return array
     */
    public function getData()
    {
        return [
            [
                $this->createParam('foo_table', 'en'),
                'DELETE FROM `foo_table`',
                [
                ],
            ],
            [
                $this->createParam('foo_table', 'en')
                    ->setWhereEquals([
                        'stringId' => 'xyz',
                        'suchField' => 'veryValue',
                    ]),
                'DELETE FROM `foo_table` WHERE `stringId` = ? AND `suchField` = ?',
                [
                    'xyz',
                    'veryValue',
                ],
            ],
            [
                $this->createParam('foo_table', 'en')
                    ->setFields([
                        'bar' => 'baz',
                    ])
                    ->setWhereExpressions([
                        new Comparison('stringId', Comparison::EQ, 'xyz'),
                    ]),
                'DELETE FROM `foo_table` WHERE `stringId` = ?',
                [
                    'xyz',
                ],
            ],
            [
                $this->createParam('foo_table', 'en')
                    ->setWhereEquals([
                        'stringId' => 'xyz',
                        'suchField' => 'veryValue',
                    ])
                    ->setWhereExpressions([
                        new Comparison('muchField', Comparison::EQ, 'wow'),
                    ]),
                'DELETE FROM `foo_table` WHERE `stringId` = ? AND `suchField` = ? AND `muchField` = ?',
                [
                    'xyz',
                    'veryValue',
                    'wow',
                ],
            ],
            [
                $this->createParam('foo_table', 'en')
                    ->setWhereExpressions([
                        new Comparison('id1', Comparison::EQ, 'id2'),
                        new Comparison('suchField', Comparison::GT, 'veryValue'),
                    ])
                    ->setWhereExpressionsFieldTypes([
                        'id1' => 'column',
                    ]),
                'DELETE FROM `foo_table` WHERE `id1` = `id2` AND `suchField` > ?',
                [
                    'veryValue',
                ],
            ],
            [
                $this->createParam('foo_table', 'en')
                    ->setWhereExpressions([
                        new Comparison('bar', Comparison::IN, '(SELECT `id` FROM `bar_table`)'),
                    ])
                    ->setWhereExpressionsFieldTypes([
                        'bar' => 'literal',
                    ]),
                'DELETE FROM `foo_table` WHERE `bar` IN (SELECT `id` FROM `bar_table`)',
                [],
            ],
            [
                $this->createParam('foo_table', 'de')
                    ->setWhereEquals([
                        'translatedField' => 'baz',
                    ]),
                'DELETE FROM `foo_table` WHERE `translatedField__de` = ?',
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
                        new Comparison('stringId', Comparison::EQ, 'xyz'),
                        new Comparison('translatedField', Comparison::GT, 'veryValue'),
                    ]),
                'DELETE FROM `foo_table` WHERE `stringId` = ? AND `translatedField__de` > ?',
                [
                    'xyz',
                    'veryValue',
                ],
            ],
            [
                $this->createParam('foo_table', 'de')
                    ->setWhereExpressions([
                        new Comparison('stringId', Comparison::IN, ['xyz', 'abc']),
                    ]),
                "DELETE FROM `foo_table` WHERE `stringId` IN ('xyz', 'abc')",
                [],
            ],
            [
                $this->createParam('foo_table', 'de')
                    ->setWhereExpressions([
                        new Comparison('stringId', Comparison::IN, ['xyz', 'abc']),
                    ])
                    ->setWhereExpressionsFieldTypes([
                        'stringId' => QueryConstants::FIELDTYPE_ARRAY,
                    ]),
                "DELETE FROM `foo_table` WHERE `stringId` IN ('xyz', 'abc')",
                [],
            ],
        ];
    }
}
