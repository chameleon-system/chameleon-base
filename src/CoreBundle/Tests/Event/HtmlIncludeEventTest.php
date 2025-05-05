<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Tests\Event;

use ChameleonSystem\CoreBundle\Event\HtmlIncludeEvent;
use PHPUnit\Framework\TestCase;

class HtmlIncludeEventTest extends TestCase
{
    /**
     * @var HtmlIncludeEvent
     */
    private $event;

    /**
     * @test
     *
     * @dataProvider dataProviderAddData
     */
    public function itShouldAddUniqueContent($initialData, $newData, $expectedData)
    {
        $this->given_we_have_an_event_with_the_following_data($initialData);
        $this->when_we_call_addData_with($newData);
        $this->then_we_expect_getData_to_return($expectedData);
    }

    public function dataProviderAddData()
    {
        return [
            [
                [], // $initialData
                ['foo' => 'bar', 'foobar'], // $newData
                ['foo' => 'bar', md5('foobar') => 'foobar'], // $expectedData
            ],
            [
                ['barz' => 'foo'], // $initialData
                ['foo' => 'bar', 'foobar'], // $newData
                ['barz' => 'foo', 'foo' => 'bar', md5('foobar') => 'foobar'], // $expectedData
            ],
            [
                ['barz' => 'foo'], // $initialData
                ['barz' => 'foo2', 'foo' => 'bar', 'foobar'], // $newData
                ['barz' => 'foo', 'foo' => 'bar', md5('foobar') => 'foobar'], // $expectedData
            ],
            [
                ['barz' => 'foo', 'foobar'], // $initialData
                ['barz' => 'foo2', 'foo' => 'bar', 'foobar'], // $newData
                ['barz' => 'foo', 'foo' => 'bar', md5('foobar') => 'foobar'], // $expectedData
            ],
        ];
    }

    private function given_we_have_an_event_with_the_following_data($initialData)
    {
        $this->event = new HtmlIncludeEvent($initialData);
    }

    private function when_we_call_addData_with($newData)
    {
        $this->event->addData($newData);
    }

    private function then_we_expect_getData_to_return($expectedData)
    {
        $this->assertEquals($expectedData, $this->event->getData());
    }
}
