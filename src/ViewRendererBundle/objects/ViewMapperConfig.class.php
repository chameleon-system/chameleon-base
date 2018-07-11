<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class ViewMapperConfig implements ViewMapperConfigInterface
{
    /**
     * @var array
     */
    private $aMapperConfig = array();

    /**
     * @param string $config
     */
    public function __construct($config)
    {
        $aConfig = explode("\n", $config);
        foreach ($aConfig as $sConfigLine) {
            $sConfigLine = trim($sConfigLine);
            if (true === empty($sConfigLine)) {
                continue;
            }
            $aTmpParts = explode('=', $sConfigLine);
            $sViewName = trim($aTmpParts[0]);
            $this->aMapperConfig[$sViewName] = array();
            if (1 === count($aTmpParts)) {
                continue;
            }

            $aTmpConfig = explode(';', trim($aTmpParts[1]));
            $this->aMapperConfig[$sViewName]['snippet'] = trim($aTmpConfig[0]);
            $this->aMapperConfig[$sViewName]['aMapper'] = array();
            if (isset($aTmpConfig[1])) {
                $this->aMapperConfig[$sViewName]['aMapper'] = explode(',', trim($aTmpConfig[1]));
                foreach ($this->aMapperConfig[$sViewName]['aMapper'] as $sKey => $sMapperConfig) {
                    $this->aMapperConfig[$sViewName]['aMapper'][$sKey] = array();
                    $arrayMapperPos = strpos($sMapperConfig, '{');
                    $varMapperPos = strpos($sMapperConfig, '[');
                    $this->aMapperConfig[$sViewName]['aMapper'][$sKey]['arrayMapping'] = null;
                    $this->aMapperConfig[$sViewName]['aMapper'][$sKey]['varMapping'] = array();
                    if (false === $arrayMapperPos && false === $varMapperPos) {
                        $this->aMapperConfig[$sViewName]['aMapper'][$sKey]['name'] = trim($sMapperConfig);
                    } else {
                        $arrayMapperPos = false === $arrayMapperPos ? PHP_INT_MAX : $arrayMapperPos;
                        $varMapperPos = false === $varMapperPos ? PHP_INT_MAX : $varMapperPos;
                        $splitPos = min($arrayMapperPos, $varMapperPos);
                        $this->aMapperConfig[$sViewName]['aMapper'][$sKey]['name'] = trim(substr($sMapperConfig, 0, $splitPos));
                        $mappings = array();
                        preg_match_all('/\[(.*?)\]/', $sMapperConfig, $mappings);
                        if (2 === count($mappings)) {
                            foreach ($mappings[1] as $mapping) {
                                $mappingParts = explode('->', $mapping);
                                $this->aMapperConfig[$sViewName]['aMapper'][$sKey]['varMapping'][$mappingParts[0]] = $mappingParts[1];
                            }
                        }
                        $mappings = array();
                        preg_match_all('/\{(.*?)\}/', $sMapperConfig, $mappings);
                        if (2 === count($mappings)) {
                            foreach ($mappings[1] as $mapping) {
                                $this->aMapperConfig[$sViewName]['aMapper'][$sKey]['arrayMapping'] = $mapping;
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAsString()
    {
        $lines = array();
        reset($this->aMapperConfig);
        foreach ($this->aMapperConfig as $config => $configData) {
            $line = $config.'='.$configData['snippet'];
            if (count($configData['aMapper']) > 0) {
                $mapper = array();
                foreach ($configData['aMapper'] as $key => $mapperConfig) {
                    $mapperString = $mapperConfig['name'];
                    if (null !== $mapperConfig['arrayMapping']) {
                        $mapperString .= '{'.$mapperConfig['arrayMapping'].'}';
                    }
                    $varMapping = array();
                    foreach ($mapperConfig['varMapping'] as $varKey => $varTarget) {
                        $varMapping[] = '['.$varKey.'->'.$varTarget.']';
                    }
                    if (count($varMapping) > 0) {
                        $mapperString .= implode('', $varMapping);
                    }
                    $mapper[] = $mapperString;
                }
                $line .= ';'.implode(',', $mapper);
            }
            $lines[] = $line;
        }

        return implode("\n", $lines);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigs()
    {
        return array_keys($this->aMapperConfig);
    }

    /**
     * {@inheritdoc}
     */
    public function getMappersForConfig($configname)
    {
        if (!array_key_exists($configname, $this->aMapperConfig)) {
            return null;
        }

        $aMappers = array();
        foreach ($this->aMapperConfig[$configname]['aMapper'] as $mapper) {
            $aMappers[] = $mapper['name'];
        }
        reset($this->aMapperConfig[$configname]['aMapper']);

        return $aMappers;
    }

    /**
     * {@inheritdoc}
     */
    public function getSnippetForConfig($configname)
    {
        if (!array_key_exists($configname, $this->aMapperConfig)) {
            return null;
        }

        return $this->aMapperConfig[$configname]['snippet'];
    }

    /**
     * {@inheritdoc}
     */
    public function getTransformationsForMapper($configname, $mappername)
    {
        if (!array_key_exists($configname, $this->aMapperConfig)) {
            return null;
        }
        foreach ($this->aMapperConfig[$configname]['aMapper'] as $mapper) {
            if ($mappername === $mapper['name']) {
                reset($this->aMapperConfig[$configname]['aMapper']);

                return $mapper['varMapping'];
            }
        }
        reset($this->aMapperConfig[$configname]['aMapper']);

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getArrayMappingForMapper($configname, $mappername)
    {
        if (!array_key_exists($configname, $this->aMapperConfig)) {
            return null;
        }

        foreach ($this->aMapperConfig[$configname]['aMapper'] as $mapper) {
            if ($mappername === $mapper['name']) {
                reset($this->aMapperConfig[$configname]['aMapper']);

                return $mapper['arrayMapping'];
            }
        }
        reset($this->aMapperConfig[$configname]['aMapper']);

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlainParsedConfig()
    {
        return $this->aMapperConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigCount()
    {
        return count($this->aMapperConfig);
    }

    /**
     * {@inheritdoc}
     */
    public function addMapper($config, $mapper, $placeAfterMapper = null)
    {
        if (false === isset($this->aMapperConfig[$config])) {
            return false;
        }
        if (false === is_array($mapper)) {
            $mapper = array(
                'arrayMapping' => null,
                'varMapping' => array(),
                'name' => $mapper,
            );
        }
        if (null === $placeAfterMapper) {
            $this->aMapperConfig[$config]['aMapper'][] = $mapper;
        } else {
            $bFound = false;
            $newMapperConfig = array();
            foreach ($this->aMapperConfig[$config]['aMapper'] as $key => $tmpMapperData) {
                $newMapperConfig[] = $tmpMapperData;
                if ($placeAfterMapper === $tmpMapperData['name']) {
                    $newMapperConfig[] = $mapper;
                    $bFound = true;
                }
            }
            reset($this->aMapperConfig[$config]['aMapper']);
            if (false === $bFound) {
                $newMapperConfig[] = $mapper;
            }
            $this->aMapperConfig[$config]['aMapper'] = $newMapperConfig;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function removeMapper($config, $mapper)
    {
        if (false === isset($this->aMapperConfig[$config])) {
            return false;
        }

        $bFound = false;
        $newMapperConfig = array();
        foreach ($this->aMapperConfig[$config]['aMapper'] as $key => $tmpMapperData) {
            if ($mapper !== $tmpMapperData['name']) {
                $newMapperConfig[] = $tmpMapperData;
            } else {
                $bFound = true;
            }
        }
        reset($this->aMapperConfig[$config]['aMapper']);
        $this->aMapperConfig[$config]['aMapper'] = $newMapperConfig;

        return $bFound;
    }

    /**
     * {@inheritdoc}
     */
    public function replaceMapper($oldMapper, $newMapper, $config = null)
    {
        if (null !== $config && false === isset($this->aMapperConfig[$config])) {
            return false;
        }

        $found = false;
        if (null === $config) {
            $configsToChange = $this->aMapperConfig;
        } else {
            $configsToChange = [
                $config => $this->aMapperConfig[$config],
            ];
        }
        foreach ($configsToChange as $configName => $configToChange) {
            $newMapperConfig = array();
            foreach ($configToChange['aMapper'] as $key => $originalMapperData) {
                if ($oldMapper === $originalMapperData['name']) {
                    $newMapperData = $originalMapperData;
                    $newMapperData['name'] = $newMapper;
                    $newMapperConfig[] = $newMapperData;
                    $found = true;
                } else {
                    $newMapperConfig[] = $originalMapperData;
                }
            }
            $this->aMapperConfig[$configName]['aMapper'] = $newMapperConfig;
        }

        return $found;
    }

    /**
     * {@inheritdoc}
     */
    public function changeSnippet($config, $newSnippet)
    {
        $this->aMapperConfig[$config]['snippet'] = $newSnippet;
    }

    /**
     * {@inheritdoc}
     */
    public function addConfig($config, $snippetName, $mapperChain)
    {
        $this->aMapperConfig[$config] = array(
            'snippet' => $snippetName,
            'aMapper' => array(),
        );
        if (false === is_array($mapperChain)) {
            $mapperChain = array($mapperChain);
        }
        /**
         * @var array $mapperChain
         */
        foreach ($mapperChain as $mapperName) {
            $this->aMapperConfig[$config]['aMapper'][] = array(
                'arrayMapping' => null,
                'varMapping' => array(),
                'name' => $mapperName,
            );
        }
    }
}
