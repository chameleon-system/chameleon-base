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
use Exception;
use IMapperCacheTrigger;
use IViewMapper;
use MapperException;
use MapperVisitor;
use MapperVisitorRestrictedProxy;

class DataMappingService implements DataMappingServiceInterface
{
    /**
     * @var IViewMapper[]
     */
    private $mappers = array();
    /**
     * @var array
     */
    private $transformations = array();
    /**
     * @var array
     */
    private $mapToArray = array();
    /**
     * @var array
     */
    private $sourceObjects = array();
    /**
     * @var MapperVisitor
     */
    private $mapperVisitor = null;
    /**
     * @var IMapperCacheTrigger
     */
    private $cacheTriggerCollector = null;

    /**
     * @var DataMappingServiceInterface[]
     */
    private $mappingServices;
    /**
     * @var DataMappingServiceHelperFactoryInterface
     */
    private $helperFactory;

    public function __construct(DataMappingServiceHelperFactoryInterface $helperFactory)
    {
        $this->helperFactory = $helperFactory;
    }

    /**
     * resets service so it can be used with new source data.
     */
    public function reset()
    {
        $this->sourceObjects = array();
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
    public function addMapper(IViewMapper $oMapper, $transformations = null, $mapToArray = null)
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

    private function createCacheTriggerCollector()
    {
        $this->cacheTriggerCollector = $this->helperFactory->createCacheTriggerCollector();
    }

    private function createMapperVisitorWithSourceData()
    {
        $this->mapperVisitor = $this->helperFactory->createMapperVisitor($this->sourceObjects);
        if (null !== $this->mappingServices) {
            $this->mapperVisitor->addMapperChains($this->mappingServices);
        }
    }

    /**
     * @throws MapperException
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
     * @param IViewMapper $mapper
     * @param array|null  $aMapperTransformations
     * @param array|null  $aMapperMapToArrayTransformations
     *
     * @throws MapperException
     */
    private function executeMapper(
        IViewMapper $mapper,
        array $aMapperTransformations = null,
        $aMapperMapToArrayTransformations = null
    ) {
        $this->applyMapperRequirementsToVisitor($mapper);
        $this->mapperVisitor->setTransformations($aMapperTransformations);
        $this->mapperVisitor->setMapToArray($aMapperMapToArrayTransformations);

        try {
            $mapper->Accept(new MapperVisitorRestrictedProxy($this->mapperVisitor), true, new \MapperCacheTriggerRestrictedProxy($this->cacheTriggerCollector));
        } catch (MapperException $e) {
            $message = 'Mapper: '.get_class($mapper).' Error: '.$e->getMessage();
            throw new MapperException($message, $e->getCode(), $e);
        } catch (Exception $e) {
            $message = 'Mapper: '.get_class($mapper).' Unexpected error: '.$e->getMessage();
            throw new MapperException($message, $e->getCode(), $e);
        }
    }

    /**
     * @param IViewMapper $mapper
     */
    private function applyMapperRequirementsToVisitor(IViewMapper $mapper)
    {
        $oRequirements = $this->helperFactory->createRequirementsVisitor();
        $mapper->GetRequirements(new \MapperRequirementsRestrictedProxy($oRequirements));
        $this->mapperVisitor->SetCurrentRequirements($oRequirements);
    }

    /**
     * @param int $mapperIndex
     *
     * @return array
     */
    private function getMapperTransformations($mapperIndex)
    {
        return $this->transformations[$mapperIndex];
    }

    /**
     * @param int $mapperIndex
     *
     * @return array
     */
    private function getMapperMapToArrayTransformations($mapperIndex)
    {
        return $this->mapToArray[$mapperIndex];
    }

    /**
     * @return DataMappingServiceResponseInterface
     */
    private function createMapperResponse()
    {
        $responseFactory = new DataMappingServiceResponseFactory();

        return $responseFactory->createResponse($this->mapperVisitor, $this->cacheTriggerCollector);
    }

    /**
     * @return bool
     */
    public function hasMappers()
    {
        return count($this->mappers) > 0;
    }

    /**
     * @return array
     */
    public function getMapperNameList()
    {
        $mapperList = array();
        reset($this->mappers);
        foreach ($this->mappers as $mapper) {
            $mapperList[] = get_class($mapper);
        }

        return $mapperList;
    }

    /**
     * @return array
     */
    public function getSourceData()
    {
        return $this->sourceObjects;
    }
}
