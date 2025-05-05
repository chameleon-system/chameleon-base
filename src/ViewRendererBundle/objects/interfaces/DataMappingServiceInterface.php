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

interface DataMappingServiceInterface
{
    public function __construct(DataMappingServiceHelperFactoryInterface $helperFactory);

    /**
     * @param \IViewMapper[] $aMappers
     *
     * @return void
     */
    public function addMappers(array $aMappers);

    /**
     * @param array<string, string>|null $transformations
     * @param string $mapToArray
     *
     * @return void
     */
    public function addMapper(\IViewMapper $oMapper, $transformations = null, $mapToArray = null);

    /**
     * @param string $key
     *
     * @return void
     */
    public function addSourceObject($key, $value);

    /**
     * @return array
     */
    public function getSourceData();

    /**
     * @return void
     */
    public function addSourceObjectsFromArray(array $aVars);

    /**
     * @return DataMappingServiceResponseInterface
     *
     * @throws \MapperException
     */
    public function performTransformation();

    /**
     * @return bool
     */
    public function hasMappers();

    /**
     * @return class-string<\IViewMapper>[]
     */
    public function getMapperNameList();

    /**
     * resets service so it can be used with new source data.
     *
     * @return void
     */
    public function reset();

    /**
     * @param string $mappingServiceName
     *
     * @return void
     */
    public function addMappingService($mappingServiceName, self $mappingService);
}
