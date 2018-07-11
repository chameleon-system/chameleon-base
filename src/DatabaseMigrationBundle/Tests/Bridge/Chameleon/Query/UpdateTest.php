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
     * @dataProvider getData
     *
     * @param MigrationQueryData $migrationQueryData
     * @param $expectedQuery
     * @param array $expectedQueryParams
     */
    public function it_should_get_query_params(MigrationQueryData $migrationQueryData, $expectedQuery, array $expectedQueryParams)
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

    /**
     * @param MigrationQueryData $migrationQueryData
     */
    private function whenICallGetQuery(MigrationQueryData $migrationQueryData)
    {
        list($this->actualQuery, $this->actualQueryParams) = $this->update->getQuery($migrationQueryData);
    }

    /**
     * @return array
     */
    public function getData()
    {
        return array(
            array(
                $this->createParam('foo_table', 'en')
                    ->setFields(array(
                        'bar' => 'baz',
                    )),
                'UPDATE `foo_table` SET `bar` = ?',
                array(
                    'baz',
                ),
            ),
            array(
                $this->createParam('foo_table', 'en')
                    ->setFields(array(
                        'bar' => 'baz',
                    ))
                    ->setWhereEquals(array(
                        'stringId' => 'xyz',
                        'suchField' => 'veryValue',
                    )),
                'UPDATE `foo_table` SET `bar` = ? WHERE `stringId` = ? AND `suchField` = ?',
                array(
                    'baz',
                    'xyz',
                    'veryValue',
                ),
            ),
            array(
                $this->createParam('foo_table', 'en')
                    ->setFields(array(
                        'bar' => 'baz',
                    ))
                    ->setWhereExpressions(array(
                        new Comparison('stringId', Comparison::EQ, 'xyz'),
                    )),
                'UPDATE `foo_table` SET `bar` = ? WHERE `stringId` = ?',
                array(
                    'baz',
                    'xyz',
                ),
            ),
            array(
                $this->createParam('foo_table', 'en')
                    ->setFields(array(
                        'bar' => 'baz',
                    ))
                    ->setWhereEquals(array(
                        'stringId' => 'xyz',
                        'suchField' => 'veryValue',
                    ))
                    ->setWhereExpressions(array(
                        new Comparison('muchField', Comparison::EQ, 'wow'),
                    )),
                'UPDATE `foo_table` SET `bar` = ? WHERE `stringId` = ? AND `suchField` = ? AND `muchField` = ?',
                array(
                    'baz',
                    'xyz',
                    'veryValue',
                    'wow',
                ),
            ),
            array(
                $this->createParam('foo_table', 'en')
                    ->setFields(array(
                        'bar' => 'baz',
                    ))
                    ->setWhereExpressions(array(
                        new Comparison('stringId', Comparison::EQ, 'xyz'),
                        new Comparison('suchField', Comparison::GT, 'veryValue'),
                    )),
                'UPDATE `foo_table` SET `bar` = ? WHERE `stringId` = ? AND `suchField` > ?',
                array(
                    'baz',
                    'xyz',
                    'veryValue',
                ),
            ),
            array(
                $this->createParam('foo_table', 'en')
                    ->setFields(array(
                        'bar' => 'baz',
                    ))
                    ->setFieldTypes(array(
                        'bar' => QueryConstants::FIELDTYPE_COLUMN,
                    )),
                'UPDATE `foo_table` SET `bar` = `baz`',
                array(),
            ),
            array(
                $this->createParam('foo_table', 'en')
                    ->setFields(array(
                        'bar' => '`baz` + 1',
                    ))
                    ->setFieldTypes(array(
                        'bar' => QueryConstants::FIELDTYPE_LITERAL,
                    )),
                'UPDATE `foo_table` SET `bar` = `baz` + 1',
                array(),
            ),
            array(
                $this->createParam('foo_table', 'en')
                    ->setFields(array(
                        'standardValue' => 'baz',
                        'columnValue' => 'baz',
                        'literalValue' => '`baz` + 1',
                    ))
                    ->setFieldTypes(array(
                        'columnValue' => QueryConstants::FIELDTYPE_COLUMN,
                        'literalValue' => QueryConstants::FIELDTYPE_LITERAL,
                    ))
                    ->setWhereExpressions(array(
                        new Comparison('stringId', Comparison::EQ, 'xyz'),
                        new Comparison('suchField', Comparison::GT, 'veryValue'),
                    )),
                'UPDATE `foo_table` SET `standardValue` = ?, `columnValue` = `baz`, `literalValue` = `baz` + 1 WHERE `stringId` = ? AND `suchField` > ?',
                array(
                    'baz',
                    'xyz',
                    'veryValue',
                ),
            ),
            array(
                $this->createParam('foo_table', 'de')
                    ->setFields(array(
                        'translatedField' => 'baz',
                    )),
                'UPDATE `foo_table` SET `translatedField__de` = ?',
                array(
                    'baz',
                ),
            ),
            array(
                $this->createParam('foo_table', 'de')
                    ->setFields(array(
                        'foo' => 'bar',
                        'translatedField' => 'baz',
                    ))
                    ->setWhereExpressions(array(
                        new Comparison('stringId', Comparison::EQ, 'xyz'),
                        new Comparison('suchField', Comparison::GT, 'veryValue'),
                    )),
                'UPDATE `foo_table` SET `foo` = ?, `translatedField__de` = ? WHERE `stringId` = ? AND `suchField` > ?',
                array(
                    'bar',
                    'baz',
                    'xyz',
                    'veryValue',
                ),
            ),
            array(
                $this->createParam('foo_table', 'de')
                    ->setFields(array(
                        'foo' => 'bar',
                    ))
                    ->setWhereEquals(array(
                        'whereEqualsField' => 'whereEqualsValue',
                    ))
                    ->setWhereExpressions(array(
                        new CompositeExpression(CompositeExpression::TYPE_OR, array(
                            new Comparison('orField1', Comparison::EQ, 'orValue1'),
                            new Comparison('orField2', Comparison::GT, 'orValue2'),
                        )),
                        new Comparison('stringId', '=', 'xyz'),
                        new Comparison('translatedField', '>', 'veryValue'),
                    ))
                    ->setWhereExpressionsFieldTypes(array(
                        'stringId' => 'column',
                    )),
                'UPDATE `foo_table` SET `foo` = ? WHERE `whereEqualsField` = ? AND (`orField1` = ? OR `orField2` > ?) AND `stringId` = `xyz` AND `translatedField__de` > ?',
                array(
                    'bar',
                    'whereEqualsValue',
                    'orValue1',
                    'orValue2',
                    'veryValue',
                ),
            ),
            array(
                $this->createParam('foo_table', 'de')
                    ->setFields(array(
                        'foo' => 'bar',
                    ))
                    ->setWhereExpressions(array(
                        new Comparison('stringId', Comparison::IN, array('xyz', 'abc')),
                    )),
                "UPDATE `foo_table` SET `foo` = ? WHERE `stringId` IN ('xyz', 'abc')",
                array(
                    'bar',
                ),
            ),
            array(
                $this->createParam('foo_table', 'de')
                    ->setFields(array(
                        'foo' => 'bar',
                    ))
                    ->setWhereExpressions(array(
                        new Comparison('stringId', Comparison::IN, array('xyz', 'abc')),
                    ))
                    ->setWhereExpressionsFieldTypes(array(
                        'stringId' => QueryConstants::FIELDTYPE_ARRAY,
                    )),
                "UPDATE `foo_table` SET `foo` = ? WHERE `stringId` IN ('xyz', 'abc')",
                array(
                    'bar',
                ),
            ),
        );
    }
}
