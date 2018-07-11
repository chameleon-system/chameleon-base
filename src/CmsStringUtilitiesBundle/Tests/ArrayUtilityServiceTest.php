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
     * @dataProvider dataProviderArrayEqual
     *
     * @param $compareName
     * @param $array1
     * @param $array2
     * @param $expectedResult
     *
     * @internal param $expectedDiff
     */
    public function it_should_be_able_to_determine_if_two_arrays_are_equal($compareName, $array1, $array2, $expectedResult)
    {
        $this->given_two_arrays($array1, $array2);
        $this->given_an_instance_of_the_array_service();
        $this->when_we_call_equal();
        $this->then_we_expect_to_get($expectedResult, $compareName);
    }

    public function dataProviderArrayEqual()
    {
        return array(
            array(
                'empty with empty',
                array(),
                array(),
                true,
            ),
            array(
                'empty with array',
                array(),
                array('foo' => 'bar'),
                false,
            ),
            array(
                'empty with complex array',
                array(),
                array('foo' => 'bar', 'test', 'subarray' => array('foo', 'bar' => 'foobar')),
                false,
            ),

            array(
                'two arrays with mixed keys',
                array('foo', 'bar' => 'foobar', 'inboth', 'inbotharray' => array('foo')),
                array('foo2', 'bar' => 'foobar2', 'inboth', 'inbotharray' => array('foo')),
                false,
            ),

            array(
                'two equal complex arrays with mixed keys',
                array('foo', 'bar' => 'foobar', 'inboth', 'inbotharray' => array('foo', 'subsub' => 'somevalue', 'subsubarray' => array('foo' => 'bar'))),
                array('bar' => 'foobar', 'foo', 'inbotharray' => array('subsub' => 'somevalue', 'foo', 'subsubarray' => array('foo' => 'bar')), 'inboth'),
                true,
            ),
        );
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
