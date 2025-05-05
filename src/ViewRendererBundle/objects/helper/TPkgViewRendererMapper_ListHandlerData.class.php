<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgViewRendererMapper_ListHandlerData
{
    /**
     * @var string
     */
    private $sourceVariableName = '';
    /**
     * @var string
     */
    private $targetVariableName = '';
    /**
     * @var string
     */
    private $snippetName = '';
    /**
     * @var array
     */
    private $mapperChain = [];
    /**
     * @var string
     */
    private $itemName = '';

    /**
     * @return string
     */
    public function getSourceVariableName()
    {
        return $this->sourceVariableName;
    }

    /**
     * @param string $sourceVariableName
     *
     * @return TPkgViewRendererMapper_ListHandlerData
     */
    public function setSourceVariableName($sourceVariableName)
    {
        $this->sourceVariableName = $sourceVariableName;

        return $this;
    }

    /**
     * @return string
     */
    public function getTargetVariableName()
    {
        return $this->targetVariableName;
    }

    /**
     * @param string $targetVariableName
     *
     * @return TPkgViewRendererMapper_ListHandlerData
     */
    public function setTargetVariableName($targetVariableName)
    {
        $this->targetVariableName = $targetVariableName;

        return $this;
    }

    /**
     * @return string
     */
    public function getSnippetName()
    {
        return $this->snippetName;
    }

    /**
     * @param string $snippetName
     *
     * @return TPkgViewRendererMapper_ListHandlerData
     */
    public function setSnippetName($snippetName)
    {
        $this->snippetName = $snippetName;

        return $this;
    }

    /**
     * @return array
     */
    public function getMapperChain()
    {
        return $this->mapperChain;
    }

    /**
     * @param array $mapperChain
     *
     * @return TPkgViewRendererMapper_ListHandlerData
     */
    public function setMapperChain($mapperChain)
    {
        $this->mapperChain = $mapperChain;

        return $this;
    }

    /**
     * @return string
     */
    public function getItemName()
    {
        return $this->itemName;
    }

    /**
     * @param string $itemName
     *
     * @return TPkgViewRendererMapper_ListHandlerData
     */
    public function setItemName($itemName)
    {
        $this->itemName = $itemName;

        return $this;
    }
}
