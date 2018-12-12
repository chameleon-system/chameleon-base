<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Tests\DatabaseAccessLayer;

use ChameleonSystem\core\DatabaseAccessLayer\QueryModifierOrderBy;
use PHPUnit\Framework\TestCase;

class QueryModifierOrderByTest extends TestCase
{
    /**
     * @var QueryModifierOrderBy
     */
    private $modifier;
    private $resultQuery;

    protected function tearDown()
    {
        parent::tearDown();
        $this->modifier = null;
        $this->resultQuery = null;
    }

    /**
     * @test
     * @dataProvider dataProviderRemoveOrderBy
     *
     * @param $query
     * @param $expectedResult
     */
    public function it_removes_order_by($query, $expectedResult)
    {
        $this->given_a_modifier();
        $this->when_we_call_getQueryWithoutOrderBy_with($query);
        $this->then_we_expect($expectedResult);
    }

    /**
     * @test
     * @dataProvider dataProviderChangeOrderBy
     *
     * @param $query
     * @param $expectedResult
     * @param array $orderBy
     */
    public function it_changes_order_by($query, $expectedResult, array $orderBy)
    {
        $this->given_a_modifier();
        $this->when_we_call_getQueryWithOrderBy_with($query, $orderBy);
        $this->then_we_expect($expectedResult);
    }

    /**
     * @test
     * @dataProvider dataProviderHandleInvalidSortDirection
     *
     * @param string $query
     * @param string $expectedResult
     * @param array  $orderBy
     */
    public function it_handles_invalid_sort_direction(string $query, string $expectedResult, array $orderBy): void
    {
        $this->given_a_modifier();
        $this->when_we_call_getQueryWithOrderBy_with($query, $orderBy);
        $this->then_we_expect($expectedResult);
    }

    private function given_a_modifier()
    {
        $this->modifier = new QueryModifierOrderBy();
    }

    private function when_we_call_getQueryWithoutOrderBy_with($query)
    {
        $this->resultQuery = $this->modifier->getQueryWithoutOrderBy($query);
    }

    private function then_we_expect($expectedResult)
    {
        $this->assertEquals($expectedResult, $this->resultQuery);
    }

    private function when_we_call_getQueryWithOrderBy_with($query, $orderBy)
    {
        $this->resultQuery = $this->modifier->getQueryWithOrderBy($query, $orderBy);
    }

    public function dataProviderRemoveOrderBy()
    {
        return array(
            array(
                'select foo from bar order by ping ASC, pong DESC',
                'select foo from bar',
            ),
            array(
                'select foo from (select bar from foo where barz=marz order by foo DESC, bar ASC) AS ping order by ping ASC, pong DESC',
                'select foo from (select bar from foo where barz=marz order by foo DESC, bar ASC) AS ping',
            ),
            array(
                'select foo from bar order by ping ASC, pong DESC LIMIT 0,10',
                'select foo from bar LIMIT 0,10',
            ),
        );
    }

    public function dataProviderChangeOrderBy()
    {
        return array(
            array(
                'select foo from bar order by ping ASC, pong DESC',
                'select foo from bar ORDER BY newping DESC, newpong ASC',
                array('newping' => 'DESC', 'newpong' => 'ASC'),
            ),
            array(
                'select foo from bar order by ping ASC, pong DESC',
                'select foo from bar ORDER BY `tbl`.`newping` DESC, `tbl2`.`newpong` ASC',
                array('`tbl`.`newping`' => 'DESC', '`tbl2`.`newpong`' => 'ASC'),
            ),
            array(
                'select foo from bar order by ping ASC, pong DESC',
                'select foo from bar',
                array(),
            ),
            array(
                'select foo from (select bar from foo where barz=marz order by foo DESC, bar ASC) AS ping order by ping ASC, pong DESC',
                'select foo from (select bar from foo where barz=marz order by foo DESC, bar ASC) AS ping ORDER BY `tbl`.`newping` DESC, `tbl2`.`newpong` ASC',
                array('`tbl`.`newping`' => 'DESC', '`tbl2`.`newpong`' => 'ASC'),
            ),
            array(
                'select foo from (select bar from foo where barz=marz order by foo DESC, bar ASC) AS ping order by ping ASC, pong DESC LIMIT 0,5',
                'select foo from (select bar from foo where barz=marz order by foo DESC, bar ASC) AS ping ORDER BY `tbl`.`newping` DESC, `tbl2`.`newpong` ASC LIMIT 0,5',
                array('`tbl`.`newping`' => 'DESC', '`tbl2`.`newpong`' => 'ASC'),
            ),
        );
    }

    public function dataProviderHandleInvalidSortDirection(): array
    {
        return [
            [
                'select foo from bar order by ping ASC, pong DESC',
                'select foo from bar ORDER BY ping ASC, newpong ASC',
                ['ping' => 'ASC', 'newpong' => 'foo'],
            ],
            [
                'select foo from bar order by ping ASC, pong DESC',
                'select foo from bar ORDER BY ping ASC, newpong DESC',
                ['ping' => '\'; -- BOBBY TABLES', 'newpong' => 'DESC'],
            ],
            [
                'select foo from bar order by ping ASC',
                'select foo from bar ORDER BY ping DESC',
                ['ping' => ' desc '],
            ],
        ];
    }
}
