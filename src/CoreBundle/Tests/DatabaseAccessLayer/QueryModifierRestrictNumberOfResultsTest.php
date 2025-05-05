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

use ChameleonSystem\core\DatabaseAccessLayer\QueryModifierRestrictNumberOfResults;
use PHPUnit\Framework\TestCase;

class QueryModifierRestrictNumberOfResultsTest extends TestCase
{
    /**
     * @var QueryModifierRestrictNumberOfResults
     */
    private $modifier;
    private $resultQuery;

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->modifier = null;
        $this->resultQuery = null;
    }

    /**
     * @test
     *
     * @dataProvider dataProvider
     */
    public function itAddsAMaxResultsRestriction($inputQuery, $expectedOutput, $limit)
    {
        $this->given_an_instance_of_the_query_max_result_modifier_with_query($inputQuery);
        $this->when_we_call_restrictToMaxNumberOfResults_with($limit);
        $this->then_we_expect_the_modified_query_to_match($expectedOutput);
    }

    private function given_an_instance_of_the_query_max_result_modifier_with_query($query)
    {
        $this->modifier = new QueryModifierRestrictNumberOfResults($query);
    }

    private function when_we_call_restrictToMaxNumberOfResults_with($limit)
    {
        $this->resultQuery = $this->modifier->restrictToMaxNumberOfResults($limit);
    }

    private function then_we_expect_the_modified_query_to_match($expectedOutput)
    {
        $this->assertEquals($expectedOutput, $this->resultQuery);
    }

    public function dataProvider()
    {
        return [
            [
                'select * from foo where bar=bas',
                'select * from foo where bar=bas LIMIT 0, 10',
                10,
            ],
            [
                'select * from foo where bar=bas LIMIT 0, 20',
                'select * from foo where bar=bas LIMIT 0, 10',
                10,
            ],
            [
                'select * from foo where bar=bas LIMIT 25, 20',
                'select * from foo where bar=bas LIMIT 25, 10',
                10,
            ],
            [
                'select * from foo where bar=bas LIMIT 0, 5',
                'select * from foo where bar=bas LIMIT 0, 5',
                10,
            ],
            [
                'select * from foo where bar=bas LIMIT 0,5',
                'select * from foo where bar=bas LIMIT 0, 5',
                10,
            ],
            [
                'select T.* from (select * from fobar limit 5, 10) as T where T.bar=bas',
                'select T.* from (select * from fobar limit 5, 10) as T where T.bar=bas LIMIT 0, 10',
                10,
            ],
            [
                'select T.* from (select * from fobar limit 5, 10) as T where T.bar=bas LIMIT 5, 15',
                'select T.* from (select * from fobar limit 5, 10) as T where T.bar=bas LIMIT 5, 10',
                10,
            ],
            [
                'select T.* from (select * from fobar limit 5, 10) as T where T.bar=bas LIMIT 5, 20',
                'select T.* from (select * from fobar limit 5, 10) as T where T.bar=bas LIMIT 5, 20',
                null,
            ],
            [
                'select T.* from (select * from fobar limit 5, 10) as T where T.bar=bas',
                'select T.* from (select * from fobar limit 5, 10) as T where T.bar=bas',
                null,
            ],
        ];
    }
}
