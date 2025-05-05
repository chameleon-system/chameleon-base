<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

interface ModuleMapperChainConfigInterface
{
    /**
     * @param string $configString
     */
    public function loadFromString($configString);

    /**
     * @return array
     */
    public function getMapperChains();

    /**
     * @return string
     */
    public function getAsString();

    /**
     * @param string $mapperChainName
     * @param string $newMapper
     * @param string|null $positionAfter
     *
     * @throws ErrorException
     */
    public function addMapperToChain($mapperChainName, $newMapper, $positionAfter = null);

    /**
     * @param string $mapperChainName
     * @param string $mapperName
     *
     * @throws ErrorException
     */
    public function removeMapperFromMapperChain($mapperChainName, $mapperName);

    /**
     * @param string $oldMapperName
     * @param string $newMapperName
     * @param string|null $mapperChainName
     *
     * @return bool
     *
     * @throws ErrorException
     */
    public function replaceMapper($oldMapperName, $newMapperName, $mapperChainName = null);

    /**
     * @param string $mapperChainName
     *
     * @throws ErrorException
     */
    public function addMapperChain($mapperChainName, array $mapperList);

    /**
     * @param string $mapperChainName
     *
     * @throws ErrorException
     */
    public function removeMapperChain($mapperChainName);
}
