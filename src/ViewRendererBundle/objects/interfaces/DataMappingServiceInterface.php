<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ViewRendererBundle\objects\interfaces;

use IViewMapper;
use MapperException;

interface DataMappingServiceInterface
{
    /**
     * @param DataMappingServiceHelperFactoryInterface $helperFactory
     */
    public function __construct(DataMappingServiceHelperFactoryInterface $helperFactory);

    /**
     * @param IViewMapper[] $aMappers
     */
    public function addMappers(array $aMappers);

    /**
     * @param IViewMapper $oMapper
     * @param array<string, string>|null  $transformations
     * @param string  $mapToArray
     */
    public function addMapper(IViewMapper $oMapper, $transformations = null, $mapToArray = null);

    /**
     * @param string $key
     * @param mixed $value
     */
    public function addSourceObject($key, $value);

    /**
     * @return array
     */
    public function getSourceData();

    /**
     * @param array $aVars
     */
    public function addSourceObjectsFromArray(array $aVars);

    /**
     * @return DataMappingServiceResponseInterface
     *
     * @throws MapperException
     */
    public function performTransformation();

    /**
     * @return bool
     */
    public function hasMappers();

    /**
     * @return array
     */
    public function getMapperNameList();

    /**
     * resets service so it can be used with new source data.
     */
    public function reset();

    /**
     * @param string                      $mappingServiceName
     * @param DataMappingServiceInterface $mappingService
     */
    public function addMappingService($mappingServiceName, self $mappingService);
}
