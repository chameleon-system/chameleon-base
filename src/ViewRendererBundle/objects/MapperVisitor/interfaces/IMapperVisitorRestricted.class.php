<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

interface IMapperVisitorRestricted
{
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
     * @param string $key
     *
     * @return mixed|MapperVirtualSourceObject
     */
    public function GetSourceObject($key);

    /**
     * @return string|null
     */
    public function getSnippetName();

    /**
     * @param string $mapperChainName
     *
     * @return array
     *
     * @throws MapperException
     */
    public function runMapperChainOn($mapperChainName, array $mapperInputData);
}
