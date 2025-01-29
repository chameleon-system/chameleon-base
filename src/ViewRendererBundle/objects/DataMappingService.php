<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ViewRendererBundle\objects;

use ChameleonSystem\ViewRendererBundle\objects\interfaces\DataMappingServiceHelperFactoryInterface;
use ChameleonSystem\ViewRendererBundle\objects\interfaces\DataMappingServiceInterface;
use ChameleonSystem\ViewRendererBundle\objects\interfaces\DataMappingServiceResponseInterface;

class DataMappingService implements DataMappingServiceInterface
{
    /**
     * @var \IViewMapper[]
     */
    private array $mappers = [];

    /**
     * @var list<array<string, string>|null>
     */
    private array $transformations = [];

    /**
     * @var list<string|null>
     */
    private array $mapToArray = [];

    /**
     * @var array<string, mixed>
     */
    private array $sourceObjects = [];

    /**
     * @var \MapperVisitor
     */
    private $mapperVisitor;

    /**
     * @var \IMapperCacheTrigger
     */
    private $cacheTriggerCollector;

    /**
     * @var DataMappingServiceInterface[]
     */
    private ?array $mappingServices = null;
    private DataMappingServiceHelperFactoryInterface $helperFactory;

    public function __construct(DataMappingServiceHelperFactoryInterface $helperFactory)
    {
        $this->helperFactory = $helperFactory;
    }

    /**
     * resets service so it can be used with new source data.
     */
    public function reset()
    {
        $this->sourceObjects = [];
        $this->cacheTriggerCollector = null;
    }

    /**
     * {@inheritdoc}
     */
    public function addMappingService($mappingServiceName, DataMappingServiceInterface $mappingService)
    {
        $this->mappingServices[$mappingServiceName] = $mappingService;
    }

    /**
     * {@inheritdoc}
     */
    public function addMappers(array $aMappers)
    {
        $this->mappers = $aMappers;
        $numberOfMappers = count($aMappers);
        if ($numberOfMappers > 0) {
            $this->transformations = array_fill(0, $numberOfMappers, null);
            $this->mapToArray = array_fill(0, $numberOfMappers, null);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addMapper(\IViewMapper $oMapper, $transformations = null, $mapToArray = null)
    {
        $this->mappers[] = $oMapper;
        $this->transformations[] = $transformations;
        $this->mapToArray[] = $mapToArray;
    }

    /**
     * {@inheritdoc}
     */
    public function addSourceObject($key, $value)
    {
        $this->sourceObjects[$key] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function addSourceObjectsFromArray(array $aVars)
    {
        $this->sourceObjects = array_merge($this->sourceObjects, $aVars);
    }

    /**
     * {@inheritdoc}
     */
    public function performTransformation()
    {
        $this->createCacheTriggerCollector();
        $this->createMapperVisitorWithSourceData();
        $this->executeMapperChain();

        return $this->createMapperResponse();
    }

    /**
     * @return void
     */
    private function createCacheTriggerCollector()
    {
        $this->cacheTriggerCollector = $this->helperFactory->createCacheTriggerCollector();
    }

    /**
     * @return void
     */
    private function createMapperVisitorWithSourceData()
    {
        $this->mapperVisitor = $this->helperFactory->createMapperVisitor($this->sourceObjects);
        if (null !== $this->mappingServices) {
            $this->mapperVisitor->addMapperChains($this->mappingServices);
        }
    }

    /**
     * @return void
     *
     * @throws \MapperException
     */
    private function executeMapperChain()
    {
        foreach ($this->mappers as $mapperIndex => $mapper) {
            $this->executeMapper(
                $mapper,
                $this->getMapperTransformations($mapperIndex),
                $this->getMapperMapToArrayTransformations($mapperIndex)
            );
        }
    }

    /**
     * @param array<string, string>|null $aMapperTransformations
     * @param string|null $aMapperMapToArrayTransformations
     *
     * @return void
     *
     * @throws \MapperException
     */
    private function executeMapper(
        \IViewMapper $mapper,
        ?array $aMapperTransformations = null,
        $aMapperMapToArrayTransformations = null
    ) {
        $this->applyMapperRequirementsToVisitor($mapper);
        $this->mapperVisitor->setTransformations($aMapperTransformations);
        $this->mapperVisitor->setMapToArray($aMapperMapToArrayTransformations);

        try {
            $mapper->Accept(new \MapperVisitorRestrictedProxy($this->mapperVisitor), true, new \MapperCacheTriggerRestrictedProxy($this->cacheTriggerCollector));
        } catch (\MapperException $e) {
            $message = 'Mapper: '.get_class($mapper).' Error: '.$e->getMessage();
            throw new \MapperException($message, (int) $e->getCode(), $e);
        } catch (\Exception $e) {
            $message = 'Mapper: '.get_class($mapper).' Unexpected error: '.$e->getMessage().' in file:'.$e->getFile().' on line:'.$e->getLine();
            throw new \MapperException($message, (int) $e->getCode(), $e);
        }
    }

    /**
     * @return void
     */
    private function applyMapperRequirementsToVisitor(\IViewMapper $mapper)
    {
        $oRequirements = $this->helperFactory->createRequirementsVisitor();
        $mapper->GetRequirements(new \MapperRequirementsRestrictedProxy($oRequirements));
        $this->mapperVisitor->SetCurrentRequirements($oRequirements);
    }

    /**
     * @param int $mapperIndex
     *
     * @return array<string, string>|null
     */
    private function getMapperTransformations($mapperIndex)
    {
        return $this->transformations[$mapperIndex];
    }

    /**
     * @param int $mapperIndex
     *
     * @return string|null
     */
    private function getMapperMapToArrayTransformations($mapperIndex)
    {
        return $this->mapToArray[$mapperIndex];
    }

    private function createMapperResponse(): DataMappingServiceResponseInterface
    {
        return (new DataMappingServiceResponseFactory())->createResponse($this->mapperVisitor, $this->cacheTriggerCollector);
    }

    /**
     * @return bool
     */
    public function hasMappers()
    {
        return count($this->mappers) > 0;
    }

    /**
     * @return class-string<\IViewMapper>[]
     */
    public function getMapperNameList()
    {
        $mapperList = [];
        reset($this->mappers);
        foreach ($this->mappers as $mapper) {
            $mapperList[] = get_class($mapper);
        }

        return $mapperList;
    }

    /**
     * @return array<string, mixed>
     */
    public function getSourceData()
    {
        return $this->sourceObjects;
    }
}
