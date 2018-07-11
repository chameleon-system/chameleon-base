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
     * @dataProvider getData
     *
     * @param MigrationQueryData $migrationQueryData
     * @param $expectedQuery
     * @param array $expectedQueryParams
     */
    public function it_should_get_query_params(MigrationQueryData $migrationQueryData, $expectedQuery, array $expectedQueryParams)
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

    /**
     * @param MigrationQueryData $migrationQueryData
     */
    private function whenICallGetQuery(MigrationQueryData $migrationQueryData)
    {
        list($this->actualQuery, $this->actualQueryParams) = $this->insert->getQuery($migrationQueryData);
    }

    /**
     * @return array
     */
    public function getData()
    {
        return array(
            array(
                $this->createParam('foo_table', 'en'),
                'INSERT INTO `foo_table`',
                array(
                ),
            ),
            array(
                $this->createParam('foo_table', 'en')
                    ->setFields(array(
                        'bar' => 'baz',
                    )),
                'INSERT INTO `foo_table` SET `bar` = ?',
                array(
                    'baz',
                ),
            ),
            array(
                $this->createParam('foo_table', 'en')
                    ->setFields(array(
                        'foo' => 'bar',
                        'bar' => 'baz',
                    ))
                    ->setWhereEquals(array(
                        'stringId' => 'xyz',
                        'suchField' => 'veryValue',
                    )),
                'INSERT INTO `foo_table` SET `foo` = ?, `bar` = ?',
                array(
                    'bar',
                    'baz',
                ),
            ),
            array(
                $this->createParam('foo_table', 'de')
                    ->setFields(array(
                        'translatedField' => 'baz',
                    )),
                'INSERT INTO `foo_table` SET `translatedField__de` = ?',
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
                        new Comparison('stringId', '=', 'xyz'),
                        new Comparison('suchField', '>', 'veryValue'),
                    )),
                'INSERT INTO `foo_table` SET `foo` = ?, `translatedField__de` = ?',
                array(
                    'bar',
                    'baz',
                ),
            ),
        );
    }
}
