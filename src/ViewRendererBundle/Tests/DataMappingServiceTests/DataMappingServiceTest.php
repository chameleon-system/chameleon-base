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

use ChameleonSystem\ViewRendererBundle\objects\DataMappingService;
use ChameleonSystem\ViewRendererBundle\objects\interfaces\DataMappingServiceHelperFactoryInterface;
use ChameleonSystem\ViewRendererBundle\objects\interfaces\DataMappingServiceResponseInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class DataMappingServiceTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var DataMappingService
     */
    private $dataMappingService;
    /**
     * @var DataMappingServiceHelperFactoryInterface|ObjectProphecy
     */
    private $mockHelperFactory;
    /**
     * @var DataMappingServiceResponseInterface
     */
    private $transformationResult;

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->dataMappingService = null;
        $this->mockHelperFactory = null;
        $this->transformationResult = null;
    }

    /**
     * @test
     *
     * @dataProvider dataProviderTransformData
     */
    public function itShouldTransformData($mockSourceData, $mockMappedData, $expectedResponse, $expectedCacheTrigger)
    {
        $this->given_a_mock_helper_factory($mockSourceData, $mockMappedData, $expectedCacheTrigger);
        $this->given_a_new_data_mapping_service_with_source_data($mockSourceData);
        $this->given_a_mock_mapper();
        $this->when_we_call_performTransformation();
        $this->then_we_should_get_a_response_with_mapped_data($expectedResponse);
        $this->then_we_should_get_a_response_with_cache_trigger($expectedCacheTrigger);
    }

    public function dataProviderTransformData()
    {
        return [
            [
                ['source' => 'somevalue'], // $mockSourceData,
                ['mapperitemkey' => 'mapperitemvalue'], // $mockMappedData,
                ['mapperitemkey' => 'mapperitemvalue'], // $expectedResponse,
                [['table' => 'sometable', 'id' => '1234']], // $expectedCacheTrigger
            ],
        ];
    }

    private function given_a_new_data_mapping_service_with_source_data($mockSourceData)
    {
        $this->dataMappingService = new DataMappingService($this->mockHelperFactory->reveal());
        $this->dataMappingService->addSourceObjectsFromArray($mockSourceData);
    }

    private function given_a_mock_mapper()
    {
        // need to simulate the visitor - we are assuming, that the visitor works
        /** @var $mockMapper \IViewMapper|ObjectProphecy */
        $mockMapper = $this->prophesize('IViewMapper');
        $mockMapper->Accept(Argument::any(), true, Argument::any())->shouldBeCalled();
        $mockMapper->GetRequirements(Argument::any())->shouldBeCalled();

        $this->dataMappingService->addMapper($mockMapper->reveal());
    }

    private function given_a_mock_helper_factory($mockSourceData, $mockMappedData, $expectedCacheTrigger)
    {
        /** @var $mockHelperFactory DataMappingServiceHelperFactoryInterface|ObjectProphecy */
        $mockHelperFactory = $this->prophesize('\ChameleonSystem\ViewRendererBundle\objects\interfaces\DataMappingServiceHelperFactoryInterface');

        /** @var $mockRequirementVisitor \IMapperRequirements|ObjectProphecy */
        $mockRequirementVisitor = $this->prophesize('IMapperRequirements');

        /** @var $mockVisitor \IMapperVisitor|ObjectProphecy */
        $mockVisitor = $this->prophesize('IMapperVisitor');
        $mockVisitor->GetMappedValues()->willReturn($mockMappedData);
        $mockVisitor->SetCurrentRequirements($mockRequirementVisitor->reveal())->shouldBeCalled();
        $mockVisitor->setTransformations(Argument::any())->shouldBeCalled();
        $mockVisitor->setMapToArray(Argument::any())->shouldBeCalled();
        $mockVisitor->getCacheTriggerCollectedForMapperChainsExecuted()->willReturn([])->shouldBeCalled();

        /** @var $mockCacheTrigger \IMapperCacheTrigger|ObjectProphecy */
        $mockCacheTrigger = $this->prophesize('IMapperCacheTrigger');
        $mockCacheTrigger->getTrigger()->willReturn($expectedCacheTrigger);

        $mockHelperFactory->createMapperVisitor($mockSourceData)->willReturn($mockVisitor->reveal());

        $mockHelperFactory->createCacheTriggerCollector()->willReturn($mockCacheTrigger->reveal());

        $mockHelperFactory->createRequirementsVisitor()->willReturn($mockRequirementVisitor->reveal());

        $this->mockHelperFactory = $mockHelperFactory;
    }

    private function when_we_call_performTransformation()
    {
        $this->transformationResult = $this->dataMappingService->performTransformation();
    }

    private function then_we_should_get_a_response_with_mapped_data($expectedResponse)
    {
        $this->assertEquals($expectedResponse, $this->transformationResult->getMappedData());
    }

    private function then_we_should_get_a_response_with_cache_trigger($expectedCacheTrigger)
    {
        $this->assertEquals($expectedCacheTrigger, $this->transformationResult->getCacheTrigger());
    }
}
