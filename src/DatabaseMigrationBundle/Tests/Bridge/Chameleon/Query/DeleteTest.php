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
     * @dataProvider getData
     *
     * @param MigrationQueryData $migrationQueryData
     * @param $expectedQuery
     * @param array $expectedQueryParams
     */
    public function it_should_get_query_params(MigrationQueryData $migrationQueryData, $expectedQuery, array $expectedQueryParams)
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

    /**
     * @param MigrationQueryData $migrationQueryData
     */
    private function whenICallGetQuery(MigrationQueryData $migrationQueryData)
    {
        list($this->actualQuery, $this->actualQueryParams) = $this->delete->getQuery($migrationQueryData);
    }

    /**
     * @return array
     */
    public function getData()
    {
        return array(
            array(
                $this->createParam('foo_table', 'en'),
                'DELETE FROM `foo_table`',
                array(
                ),
            ),
            array(
                $this->createParam('foo_table', 'en')
                    ->setWhereEquals(array(
                        'stringId' => 'xyz',
                        'suchField' => 'veryValue',
                    )),
                'DELETE FROM `foo_table` WHERE `stringId` = ? AND `suchField` = ?',
                array(
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
                'DELETE FROM `foo_table` WHERE `stringId` = ?',
                array(
                    'xyz',
                ),
            ),
            array(
                $this->createParam('foo_table', 'en')
                    ->setWhereEquals(array(
                        'stringId' => 'xyz',
                        'suchField' => 'veryValue',
                    ))
                    ->setWhereExpressions(array(
                        new Comparison('muchField', Comparison::EQ, 'wow'),
                    )),
                'DELETE FROM `foo_table` WHERE `stringId` = ? AND `suchField` = ? AND `muchField` = ?',
                array(
                    'xyz',
                    'veryValue',
                    'wow',
                ),
            ),
            array(
                $this->createParam('foo_table', 'en')
                    ->setWhereExpressions(array(
                        new Comparison('id1', Comparison::EQ, 'id2'),
                        new Comparison('suchField', Comparison::GT, 'veryValue'),
                    ))
                    ->setWhereExpressionsFieldTypes(array(
                        'id1' => 'column',
                    )),
                'DELETE FROM `foo_table` WHERE `id1` = `id2` AND `suchField` > ?',
                array(
                    'veryValue',
                ),
            ),
            array(
                $this->createParam('foo_table', 'en')
                    ->setWhereExpressions(array(
                        new Comparison('bar', Comparison::IN, '(SELECT `id` FROM `bar_table`)'),
                    ))
                    ->setWhereExpressionsFieldTypes(array(
                        'bar' => 'literal',
                    )),
                'DELETE FROM `foo_table` WHERE `bar` IN (SELECT `id` FROM `bar_table`)',
                array(),
            ),
            array(
                $this->createParam('foo_table', 'de')
                    ->setWhereEquals(array(
                        'translatedField' => 'baz',
                    )),
                'DELETE FROM `foo_table` WHERE `translatedField__de` = ?',
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
                        new Comparison('translatedField', Comparison::GT, 'veryValue'),
                    )),
                'DELETE FROM `foo_table` WHERE `stringId` = ? AND `translatedField__de` > ?',
                array(
                    'xyz',
                    'veryValue',
                ),
            ),
            array(
                $this->createParam('foo_table', 'de')
                    ->setWhereExpressions(array(
                        new Comparison('stringId', Comparison::IN, array('xyz', 'abc')),
                    )),
                "DELETE FROM `foo_table` WHERE `stringId` IN ('xyz', 'abc')",
                array(),
            ),
            array(
                $this->createParam('foo_table', 'de')
                    ->setWhereExpressions(array(
                        new Comparison('stringId', Comparison::IN, array('xyz', 'abc')),
                    ))
                    ->setWhereExpressionsFieldTypes(array(
                        'stringId' => QueryConstants::FIELDTYPE_ARRAY,
                    )),
                "DELETE FROM `foo_table` WHERE `stringId` IN ('xyz', 'abc')",
                array(),
            ),
        );
    }
}
