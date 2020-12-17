<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ViewRendererBundle\Tests\DataMappingServiceTests;

use ChameleonSystem\ViewRendererBundle\objects\DataMappingServiceResponseFactory;
use ChameleonSystem\ViewRendererBundle\objects\interfaces\DataMappingServiceResponseInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

class DataMappingServiceResponseFactoryTest extends TestCase
{
    /**
     * @var DataMappingServiceResponseFactory
     */
    private $responseFactory;
    /**
     * @var \IMapperCacheTrigger
     */
    private $mockMapperCacheTrigger;
    /**
     * @var \IMapperVisitor
     */
    private $mockVisitor;
    /**
     * @var DataMappingServiceResponseInterface
     */
    private $response;

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->response = null;
        $this->mockVisitor = null;
        $this->mockMapperCacheTrigger = null;
        $this->responseFactory = null;
    }

    /**
     * @test
     * @dataProvider dataProvider
     *
     * @param $responseData
     * @param $mapperCacheTrigger
     * @param $cacheTriggerFromSubCalls
     * @param $expectedCacheTrigger
     */
    public function it_should_create_a_response($responseData, $mapperCacheTrigger, $cacheTriggerFromSubCalls, $expectedCacheTrigger)
    {
        $this->given_a_response_factory();
        $this->given_a_mock_mapper_cache_trigger_with($mapperCacheTrigger);
        $this->given_a_mock_visitor_with($responseData, $cacheTriggerFromSubCalls);
        $this->when_we_create_a_response();
        $this->then_the_response_should_have_data_matching($responseData);
        $this->then_the_response_should_have_cache_trigger($expectedCacheTrigger);
    }

    public function dataProvider()
    {
        return array(
            array(
                array('item' => 'value'), // responseData
                array(array('table' => 'tab1', 'id' => '123')), // mapperCacheTrigger
                array(array('table' => 'tab2', 'id' => '1234')), // $cacheTriggerFromSubCalls
                array(array('table' => 'tab1', 'id' => '123'), array('table' => 'tab2', 'id' => '1234')), // $expectedCacheTrigger
            ),
            array(
                array('item' => 'value'), // responseData
                array(array('table' => 'tab1', 'id' => '123')), // mapperCacheTrigger
                array(), // $cacheTriggerFromSubCalls
                array(array('table' => 'tab1', 'id' => '123')), // $expectedCacheTrigger
            ),
            array(
                array('item' => 'value'), // responseData
                array(), // mapperCacheTrigger
                array(array('table' => 'tab2', 'id' => '1233')), // $cacheTriggerFromSubCalls
                array(array('table' => 'tab2', 'id' => '1233')), // $expectedCacheTrigger
            ),
            array(
                array('item' => 'value'), // responseData
                null, // mapperCacheTrigger
                array(array('table' => 'tab2', 'id' => '1233')), // $cacheTriggerFromSubCalls
                array(array('table' => 'tab2', 'id' => '1233')), // $expectedCacheTrigger
            ),
        );
    }

    private function given_a_response_factory()
    {
        $this->responseFactory = new DataMappingServiceResponseFactory();
    }

    private function given_a_mock_mapper_cache_trigger_with($mapperCacheTrigger)
    {
        /** @var $mockMapperCacheTrigger \IMapperCacheTrigger|ObjectProphecy */
        $mockMapperCacheTrigger = $this->prophesize('IMapperCacheTrigger');
        $mockMapperCacheTrigger->getTrigger()->willReturn($mapperCacheTrigger);
        $this->mockMapperCacheTrigger = $mockMapperCacheTrigger->reveal();
    }

    private function given_a_mock_visitor_with($responseData, $cacheTriggerFromSubCalls)
    {
        /** @var $mockVisitor \IMapperVisitor|ObjectProphecy */
        $mockVisitor = $this->prophesize('IMapperVisitor');
        $mockVisitor->GetMappedValues()->willReturn($responseData);
        $mockVisitor->getCacheTriggerCollectedForMapperChainsExecuted()->willReturn($cacheTriggerFromSubCalls);
        $this->mockVisitor = $mockVisitor->reveal();
    }

    private function when_we_create_a_response()
    {
        $this->response = $this->responseFactory->createResponse($this->mockVisitor, $this->mockMapperCacheTrigger);
    }

    private function then_the_response_should_have_data_matching($responseData)
    {
        $this->assertEquals($responseData, $this->response->getMappedData());
    }

    private function then_the_response_should_have_cache_trigger($expectedCacheTrigger)
    {
        $this->assertEquals($expectedCacheTrigger, $this->response->getCacheTrigger());
    }
}
