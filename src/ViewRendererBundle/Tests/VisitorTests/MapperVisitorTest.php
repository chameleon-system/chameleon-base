<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ViewRendererBundle\Tests\VisitorTests;

use ChameleonSystem\ViewRendererBundle\objects\interfaces\DataMappingServiceInterface;
use ChameleonSystem\ViewRendererBundle\objects\interfaces\DataMappingServiceResponseInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class MapperVisitorTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var \MapperVisitor
     */
    private $visitor;
    private $mapperChainResponse;

    /**
     * @var DataMappingServiceInterface|ObjectProphecy
     */
    private $mockDataTransformationService;

    /**
     * @test
     */
    public function itShouldBePossibleToRunMapperAMapperChainOnData()
    {
        $sourceData = ['foo' => 'bar'];
        $itemData = ['item' => ['foo' => 'bar']];
        $expectedTransformedItemData = ['bar' => 'foo'];
        $mapperChainName = 'mockMapperChain';
        $cacheTriggers = [];
        $this->given_a_visitor_with_source_data($sourceData);
        $this->given_a_mock_mapper_chain_that_returns_x_and_adds_cache_triggers_y($expectedTransformedItemData, $cacheTriggers);
        $this->when_we_run_the_mapper_chain_with($mapperChainName, $itemData);
        $this->then_we_expect_to_get_a_transformed_item_matching($expectedTransformedItemData);
        $this->then_we_expect_that_getCacheTriggerCollectedForMapperChainsExecuted_returns($cacheTriggers);
    }

    private function given_a_visitor_with_source_data($sourceData)
    {
        $this->visitor = new \MapperVisitor($sourceData);
    }

    private function given_a_mock_mapper_chain_that_returns_x_and_adds_cache_triggers_y(
        $expectedTransformedItemData,
        $cacheTriggers
    ) {
        /** @var $mockResponse DataMappingServiceResponseInterface|ObjectProphecy */
        $mockResponse = $this->prophesize('\ChameleonSystem\ViewRendererBundle\objects\interfaces\DataMappingServiceResponseInterface');
        $mockResponse->getMappedData()->willReturn($expectedTransformedItemData);
        $mockResponse->getCacheTrigger()->willReturn($cacheTriggers);

        $this->mockDataTransformationService = $this->prophesize('\ChameleonSystem\ViewRendererBundle\objects\interfaces\DataMappingServiceInterface');
        $this->mockDataTransformationService->performTransformation()->willReturn($mockResponse->reveal());
    }

    private function when_we_run_the_mapper_chain_with($mapperChainName, $itemData)
    {
        $this->mockDataTransformationService->reset()->shouldBeCalled();
        $this->mockDataTransformationService->addSourceObjectsFromArray($itemData)->shouldBeCalled();
        $this->visitor->addMapperChains([$mapperChainName => $this->mockDataTransformationService->reveal()]);
        $this->mapperChainResponse = $this->visitor->runMapperChainOn($mapperChainName, $itemData);
    }

    private function then_we_expect_to_get_a_transformed_item_matching($expectedTransformedItemData)
    {
        $this->assertEquals($expectedTransformedItemData, $this->mapperChainResponse);
    }

    private function then_we_expect_that_getCacheTriggerCollectedForMapperChainsExecuted_returns($cacheTriggers)
    {
        $this->assertEquals($cacheTriggers, $this->visitor->getCacheTriggerCollectedForMapperChainsExecuted());
    }
}
