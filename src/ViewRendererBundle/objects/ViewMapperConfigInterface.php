<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

interface ViewMapperConfigInterface
{
    public function getAsString(): string;

    /**
     * @return string[]
     */
    public function getConfigs();

    /**
     * @param string $configname
     *
     * @return array|null
     */
    public function getMappersForConfig($configname);

    /**
     * @param string $configname
     *
     * @return string|null
     */
    public function getSnippetForConfig($configname);

    /**
     * @param string $configname
     * @param string $mappername
     *
     * @return array|null
     */
    public function getTransformationsForMapper($configname, $mappername);

    /**
     * @param string $configname
     * @param string $mappername
     *
     * @return string|null
     */
    public function getArrayMappingForMapper($configname, $mappername);

    /**
     * @return array
     */
    public function getPlainParsedConfig();

    /**
     * @return int
     */
    public function getConfigCount();

    /**
     * @param string $config
     * @param object|array $mapper - may be an object or a mapper config in the form array('arrayMapping'=>array(),'varMapping'=>'','name'=>'')
     * @param string|null $placeAfterMapper
     *
     * @return bool
     */
    public function addMapper($config, $mapper, $placeAfterMapper = null);

    /**
     * @param string $config
     * @param string $mapper
     *
     * @return bool
     */
    public function removeMapper($config, $mapper);

    /**
     * @param string $oldMapper
     * @param string $newMapper
     * @param string|null $config
     *
     * @return bool
     */
    public function replaceMapper($oldMapper, $newMapper, $config = null);

    /**
     * @param string $config
     * @param string $newSnippet
     *
     * @return void
     */
    public function changeSnippet($config, $newSnippet);

    /**
     * @param string $config
     * @param string $snippetName
     * @param array|string $mapperChain - array of mapper names or control strings (ie. this my be myMapperClass or myMapperClass{arrayMapName}[key1->key2][key2->key4] for every mapper
     *
     * @return void
     */
    public function addConfig($config, $snippetName, $mapperChain);
}
