<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ShopBundle\Tests;

use ChameleonSystem\CmsStringUtilitiesBundle\Service\ArrayUtilityService;
use PHPUnit\Framework\TestCase;

class ArrayUtilityServiceTest extends TestCase
{
    private $dataArray1;
    private $dataArray2;
    /**
     * @var ArrayUtilityService
     */
    private $testService;
    private $resultData;

    /**
     * @test
     *
     * @dataProvider dataProviderArrayEqual
     *
     * @internal param $expectedDiff
     */
    public function itShouldBeAbleToDetermineIfTwoArraysAreEqual($compareName, $array1, $array2, $expectedResult)
    {
        $this->given_two_arrays($array1, $array2);
        $this->given_an_instance_of_the_array_service();
        $this->when_we_call_equal();
        $this->then_we_expect_to_get($expectedResult, $compareName);
    }

    public function dataProviderArrayEqual()
    {
        return [
            [
                'empty with empty',
                [],
                [],
                true,
            ],
            [
                'empty with array',
                [],
                ['foo' => 'bar'],
                false,
            ],
            [
                'empty with complex array',
                [],
                ['foo' => 'bar', 'test', 'subarray' => ['foo', 'bar' => 'foobar']],
                false,
            ],

            [
                'two arrays with mixed keys',
                ['foo', 'bar' => 'foobar', 'inboth', 'inbotharray' => ['foo']],
                ['foo2', 'bar' => 'foobar2', 'inboth', 'inbotharray' => ['foo']],
                false,
            ],

            [
                'two equal complex arrays with mixed keys',
                ['foo', 'bar' => 'foobar', 'inboth', 'inbotharray' => ['foo', 'subsub' => 'somevalue', 'subsubarray' => ['foo' => 'bar']]],
                ['bar' => 'foobar', 'foo', 'inbotharray' => ['subsub' => 'somevalue', 'foo', 'subsubarray' => ['foo' => 'bar']], 'inboth'],
                true,
            ],
        ];
    }

    private function given_two_arrays($array1, $array2)
    {
        $this->dataArray1 = $array1;
        $this->dataArray2 = $array2;
    }

    private function given_an_instance_of_the_array_service()
    {
        $this->testService = new ArrayUtilityService();
    }

    private function when_we_call_equal()
    {
        $this->resultData = $this->testService->equal($this->dataArray1, $this->dataArray2);
    }

    private function then_we_expect_to_get($expectedResult, $compareName)
    {
        $this->assertEquals($expectedResult, $this->resultData, 'failed for: '.$compareName);
    }
}
