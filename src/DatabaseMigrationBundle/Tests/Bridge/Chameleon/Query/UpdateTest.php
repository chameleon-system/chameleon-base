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
use ChameleonSystem\DatabaseMigrationBundle\Bridge\Chameleon\Query\Update;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Expr\CompositeExpression;

class UpdateTest extends AbstractQueryTestCase
{
    /**
     * @var Update
     */
    private $update;

    /**
     * @test
     *
     * @dataProvider getData
     */
    public function itShouldGetQueryParams(MigrationQueryData $migrationQueryData, $expectedQuery, array $expectedQueryParams)
    {
        $this->givenDependencies();
        $this->givenAnUpdater();
        $this->whenICallGetQuery($migrationQueryData);
        $this->thenIShouldGetQueryAndQueryParams($expectedQuery, $expectedQueryParams);
    }

    private function givenAnUpdater()
    {
        $this->update = new Update($this->databaseConnection->reveal(), $this->dataAccess->reveal());
    }

    private function whenICallGetQuery(MigrationQueryData $migrationQueryData)
    {
        list($this->actualQuery, $this->actualQueryParams) = $this->update->getQuery($migrationQueryData);
    }

    /**
     * @return array
     */
    public function getData()
    {
        return [
            [
                $this->createParam('foo_table', 'en')
                    ->setFields([
                        'bar' => 'baz',
                    ]),
                'UPDATE `foo_table` SET `bar` = ?',
                [
                    'baz',
                ],
            ],
            [
                $this->createParam('foo_table', 'en')
                    ->setFields([
                        'bar' => 'baz',
                    ])
                    ->setWhereEquals([
                        'stringId' => 'xyz',
                        'suchField' => 'veryValue',
                    ]),
                'UPDATE `foo_table` SET `bar` = ? WHERE `stringId` = ? AND `suchField` = ?',
                [
                    'baz',
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
                'UPDATE `foo_table` SET `bar` = ? WHERE `stringId` = ?',
                [
                    'baz',
                    'xyz',
                ],
            ],
            [
                $this->createParam('foo_table', 'en')
                    ->setFields([
                        'bar' => 'baz',
                    ])
                    ->setWhereEquals([
                        'stringId' => 'xyz',
                        'suchField' => 'veryValue',
                    ])
                    ->setWhereExpressions([
                        new Comparison('muchField', Comparison::EQ, 'wow'),
                    ]),
                'UPDATE `foo_table` SET `bar` = ? WHERE `stringId` = ? AND `suchField` = ? AND `muchField` = ?',
                [
                    'baz',
                    'xyz',
                    'veryValue',
                    'wow',
                ],
            ],
            [
                $this->createParam('foo_table', 'en')
                    ->setFields([
                        'bar' => 'baz',
                    ])
                    ->setWhereExpressions([
                        new Comparison('stringId', Comparison::EQ, 'xyz'),
                        new Comparison('suchField', Comparison::GT, 'veryValue'),
                    ]),
                'UPDATE `foo_table` SET `bar` = ? WHERE `stringId` = ? AND `suchField` > ?',
                [
                    'baz',
                    'xyz',
                    'veryValue',
                ],
            ],
            [
                $this->createParam('foo_table', 'en')
                    ->setFields([
                        'bar' => 'baz',
                    ])
                    ->setFieldTypes([
                        'bar' => QueryConstants::FIELDTYPE_COLUMN,
                    ]),
                'UPDATE `foo_table` SET `bar` = `baz`',
                [],
            ],
            [
                $this->createParam('foo_table', 'en')
                    ->setFields([
                        'bar' => '`baz` + 1',
                    ])
                    ->setFieldTypes([
                        'bar' => QueryConstants::FIELDTYPE_LITERAL,
                    ]),
                'UPDATE `foo_table` SET `bar` = `baz` + 1',
                [],
            ],
            [
                $this->createParam('foo_table', 'en')
                    ->setFields([
                        'standardValue' => 'baz',
                        'columnValue' => 'baz',
                        'literalValue' => '`baz` + 1',
                    ])
                    ->setFieldTypes([
                        'columnValue' => QueryConstants::FIELDTYPE_COLUMN,
                        'literalValue' => QueryConstants::FIELDTYPE_LITERAL,
                    ])
                    ->setWhereExpressions([
                        new Comparison('stringId', Comparison::EQ, 'xyz'),
                        new Comparison('suchField', Comparison::GT, 'veryValue'),
                    ]),
                'UPDATE `foo_table` SET `standardValue` = ?, `columnValue` = `baz`, `literalValue` = `baz` + 1 WHERE `stringId` = ? AND `suchField` > ?',
                [
                    'baz',
                    'xyz',
                    'veryValue',
                ],
            ],
            [
                $this->createParam('foo_table', 'de')
                    ->setFields([
                        'translatedField' => 'baz',
                    ]),
                'UPDATE `foo_table` SET `translatedField__de` = ?',
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
                        new Comparison('suchField', Comparison::GT, 'veryValue'),
                    ]),
                'UPDATE `foo_table` SET `foo` = ?, `translatedField__de` = ? WHERE `stringId` = ? AND `suchField` > ?',
                [
                    'bar',
                    'baz',
                    'xyz',
                    'veryValue',
                ],
            ],
            [
                $this->createParam('foo_table', 'de')
                    ->setFields([
                        'foo' => 'bar',
                    ])
                    ->setWhereEquals([
                        'whereEqualsField' => 'whereEqualsValue',
                    ])
                    ->setWhereExpressions([
                        new CompositeExpression(CompositeExpression::TYPE_OR, [
                            new Comparison('orField1', Comparison::EQ, 'orValue1'),
                            new Comparison('orField2', Comparison::GT, 'orValue2'),
                        ]),
                        new Comparison('stringId', '=', 'xyz'),
                        new Comparison('translatedField', '>', 'veryValue'),
                    ])
                    ->setWhereExpressionsFieldTypes([
                        'stringId' => 'column',
                    ]),
                'UPDATE `foo_table` SET `foo` = ? WHERE `whereEqualsField` = ? AND (`orField1` = ? OR `orField2` > ?) AND `stringId` = `xyz` AND `translatedField__de` > ?',
                [
                    'bar',
                    'whereEqualsValue',
                    'orValue1',
                    'orValue2',
                    'veryValue',
                ],
            ],
            [
                $this->createParam('foo_table', 'de')
                    ->setFields([
                        'foo' => 'bar',
                    ])
                    ->setWhereExpressions([
                        new Comparison('stringId', Comparison::IN, ['xyz', 'abc']),
                    ]),
                "UPDATE `foo_table` SET `foo` = ? WHERE `stringId` IN ('xyz', 'abc')",
                [
                    'bar',
                ],
            ],
            [
                $this->createParam('foo_table', 'de')
                    ->setFields([
                        'foo' => 'bar',
                    ])
                    ->setWhereExpressions([
                        new Comparison('stringId', Comparison::IN, ['xyz', 'abc']),
                    ])
                    ->setWhereExpressionsFieldTypes([
                        'stringId' => QueryConstants::FIELDTYPE_ARRAY,
                    ]),
                "UPDATE `foo_table` SET `foo` = ? WHERE `stringId` IN ('xyz', 'abc')",
                [
                    'bar',
                ],
            ],
        ];
    }
}
