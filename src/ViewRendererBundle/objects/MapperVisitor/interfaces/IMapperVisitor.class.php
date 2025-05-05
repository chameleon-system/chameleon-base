<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\ViewRendererBundle\objects\interfaces\DataMappingServiceInterface;

interface IMapperVisitor
{
    /**
     * @return array
     */
    public function GetMappedValues();

    /**
     * @return void
     */
    public function SetCurrentRequirements(IMapperRequirements $oRequirements);

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasSourceObject($key);

    /**
     * @param string $key
     * @param bool $bCheckRequirements
     *
     * @throws MapperException
     */
    public function GetSourceObject($key, $bCheckRequirements = true);

    /**
     * @param string $key
     *
     * @return void
     */
    public function SetMappedValue($key, $value);

    /**
     * add a set of data to the visitor from an array.
     *
     * @param array $aData
     *
     * @return void
     */
    public function SetMappedValueFromArray($aData);

    /**
     * @param string $sSnippet
     *
     * @return void
     */
    public function setSnippetName($sSnippet);

    /**
     * @return string|null
     */
    public function getSnippetName();

    /**
     * Used by the MapperVisitorPayloadProxy to determine if it can return a default value for a source object
     * when used in ConfigureCaching.
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasSetRequirementWithDefaultValue($key);

    /**
     * @param string $mapperChainName
     *
     * @return array
     *
     * @throws MapperException
     */
    public function runMapperChainOn($mapperChainName, array $mapperInputData);

    /**
     * @param array<string, DataMappingServiceInterface> $mapperChains assoc array. name => DataMappingServiceInterface
     *
     * @return void
     */
    public function addMapperChains(array $mapperChains);

    /**
     * @return array
     */
    public function getCacheTriggerCollectedForMapperChainsExecuted();

    /**
     * @param array<string, string> $transformations
     *
     * @return void
     */
    public function setTransformations($transformations);

    /**
     * @param string $arrayName
     *
     * @return void
     */
    public function setMapToArray($arrayName);
}
