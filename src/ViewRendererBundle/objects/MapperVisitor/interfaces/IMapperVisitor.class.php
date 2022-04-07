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
     * @param IMapperRequirements $oRequirements
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
     * @param bool   $bCheckRequirements
     *
     * @return mixed
     *
     * @throws MapperException
     */
    public function GetSourceObject($key, $bCheckRequirements = true);

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function SetMappedValue($key, $value);

    /**
     * add a set of data to the visitor from an array.
     *
     * @param array $aData
     *
     * @return
     */
    public function SetMappedValueFromArray($aData);

    /**
     * @param string $sSnippet
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
     * @param array  $mapperInputData
     *
     * @return array
     *
     * @throws MapperException
     */
    public function runMapperChainOn($mapperChainName, array $mapperInputData);

    /**
     * @param DataMappingServiceInterface[] $mapperChains assoc array. name => DataMappingServiceInterface
     */
    public function addMapperChains(array $mapperChains);

    /**
     * @return array
     */
    public function getCacheTriggerCollectedForMapperChainsExecuted();

    /**
     * @param array<string, string> $transformations
     */
    public function setTransformations($transformations);

    /**
     * @param string $arrayName
     */
    public function setMapToArray($arrayName);
}
