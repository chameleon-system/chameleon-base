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

/**
 * The visitor used internally by the ViewRenderer.
 */
class MapperVisitor implements IMapperVisitor
{
    /**
     * @var array
     */
    private $aSourceObjects = [];
    /**
     * @var array
     */
    private $aMappedValues = [];
    /**
     * @var MapperRequirements
     */
    private $oCurrentRequirements;
    /**
     * @var string|null
     */
    private $sSnippetName;
    /**
     * @var array|null
     */
    private $transformations;
    /**
     * @var string|null
     */
    private $mapToArray;
    /**
     * @var DataMappingServiceInterface[]
     */
    private $mapperChains = [];
    /**
     * @var array
     */
    private $mapperChainCacheTriggerCollection = [];

    /**
     * @param array $aSourceObjects
     */
    public function __construct($aSourceObjects = [])
    {
        $this->AddSourceObjects($aSourceObjects);
    }

    /**
     * do not call this.
     *
     * @param array $aSourceObjects
     *
     * @return void
     */
    protected function AddSourceObjects($aSourceObjects)
    {
        $this->aSourceObjects = $aSourceObjects;
    }

    /**
     * @param string $key
     *
     * @throws MapperException
     */
    private function GetMappedValue($key)
    {
        if (!isset($this->aMappedValues[$key])) {
            return null;
        }

        return $this->aMappedValues[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function SetMappedValue($key, $value)
    {
        if (null !== $this->transformations && isset($this->transformations[$key])) {
            $key = $this->transformations[$key];
        }
        if (null !== $this->mapToArray) {
            if (!isset($this->aMappedValues[$this->mapToArray]) || !is_array($this->aMappedValues[$this->mapToArray])) {
                $this->aMappedValues[$this->mapToArray] = [];
            }
            $this->aMappedValues[$this->mapToArray][$key] = $value;
        } else {
            $this->aMappedValues[$key] = $value;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function SetMappedValueFromArray($aData)
    {
        foreach ($aData as $key => $value) {
            $this->SetMappedValue($key, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function GetSourceObject($key, $bCheckRequirements = true)
    {
        if (true === $bCheckRequirements) {
            if (null === $this->oCurrentRequirements) {
                throw new MapperException('No requirements set');
            }
            if (!$this->oCurrentRequirements->CanHaveSourceObject($key)) {
                throw new MapperException(
                    'Access to the source object "'.$key.'"  not allowed due to missing requirement'
                );
            }
        }

        // try mapped value first
        $oSourceObject = $this->GetMappedValue($key);
        if (null === $oSourceObject) {
            $oSourceObject = $this->hasSourceObject(
                $key
            ) ? $this->aSourceObjects[$key] : $this->oCurrentRequirements->getSourceObjectDefault($key);
        }

        if (true === $bCheckRequirements) {
            /**
             * @var array|null $aTypes
             */
            $aTypes = $this->oCurrentRequirements->getSourceObjectType($key);
            $bValidType = false;
            if (null !== $aTypes && is_object($oSourceObject)) {
                foreach ($aTypes as $sType) {
                    if (true === ($oSourceObject instanceof $sType)) {
                        $bValidType = true;
                        break;
                    }
                }
                if (false === $bValidType) {
                    $sTypes = implode(' or ', $aTypes);
                    throw new MapperException(
                        "{$key} must be of type {$sTypes} - but is of type ".get_class($oSourceObject)
                    );
                }
            }
            if (null === $oSourceObject && false === $this->oCurrentRequirements->getSourceObjectOptional($key)) {
                throw new MapperException($key.' is not optional and must be set');
            }
        }

        return $oSourceObject;
    }

    /**
     * {@inheritdoc}
     */
    public function GetMappedValues()
    {
        return $this->aMappedValues;
    }

    /**
     * {@inheritdoc}
     */
    public function SetCurrentRequirements(IMapperRequirements $oRequirements)
    {
        $this->oCurrentRequirements = $oRequirements;
    }

    /**
     * {@inheritdoc}
     */
    public function hasSetRequirementWithDefaultValue($key)
    {
        return $this->oCurrentRequirements->CanHaveSourceObject(
            $key
        ) && (null !== $this->oCurrentRequirements->getSourceObjectDefault($key));
    }

    /**
     * {@inheritdoc}
     */
    public function hasSourceObject($key)
    {
        return true === isset($this->aSourceObjects[$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function setSnippetName($sSnippet)
    {
        $this->sSnippetName = $sSnippet;
    }

    /**
     * {@inheritdoc}
     */
    public function getSnippetName()
    {
        return $this->sSnippetName;
    }

    /**
     * {@inheritdoc}
     *
     * @param array<string, string>|null $transformations
     *
     * @return void
     */
    public function setTransformations($transformations)
    {
        $this->transformations = $transformations;
    }

    /**
     * {@inheritdoc}
     */
    public function setMapToArray($arrayName)
    {
        $this->mapToArray = $arrayName;
    }

    /**
     * {@inheritdoc}
     */
    public function runMapperChainOn($mapperChainName, array $mapperInputData)
    {
        if (false === isset($this->mapperChains[$mapperChainName])) {
            throw new MapperException(
                "requested mapper chain {$mapperChainName} can not be found. the following mapper chains are registered: ".implode(
                    array_keys($this->mapperChains)
                )
            );
        }

        $this->mapperChains[$mapperChainName]->reset();
        $this->mapperChains[$mapperChainName]->addSourceObjectsFromArray($mapperInputData);
        $response = $this->mapperChains[$mapperChainName]->performTransformation();
        $cacheTrigger = $response->getCacheTrigger();
        if (count($cacheTrigger) > 0) {
            $this->mapperChainCacheTriggerCollection = $this->mergeCacheTrigger(
                $this->mapperChainCacheTriggerCollection,
                $cacheTrigger
            );
        }

        return $response->getMappedData();
    }

    /**
     * {@inheritdoc}
     */
    public function addMapperChains(array $mapperChains)
    {
        $this->mapperChains = $mapperChains;
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheTriggerCollectedForMapperChainsExecuted()
    {
        return $this->mapperChainCacheTriggerCollection;
    }

    /**
     * @param array $mapperChainCacheTriggerCollection
     * @param array $cacheTrigger
     *
     * @return array
     */
    private function mergeCacheTrigger($mapperChainCacheTriggerCollection, $cacheTrigger)
    {
        $completeList = array_merge($mapperChainCacheTriggerCollection, $cacheTrigger);
        $finalList = [];
        foreach ($completeList as $cacheTrigger) {
            $id = (true === isset($cacheTrigger['id']) && null !== $cacheTrigger['id'] && '' !== $cacheTrigger['id']) ? $cacheTrigger['id'] : '-';
            $triggerKey = $cacheTrigger['table'].':'.$id;
            if (isset($finalList[$triggerKey])) {
                continue;
            }
            $finalList[$triggerKey] = $cacheTrigger;
        }

        return array_values($finalList);
    }
}
