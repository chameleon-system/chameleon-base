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
    private array $mapperConfig = [];

    public function __construct(string $config)
    {
        $configLines = explode("\n", $config);

        foreach ($configLines as $line) {
            $line = trim($line);

            if (empty($line)) {
                continue;
            }

            $this->processConfigLine($line);
        }
    }

    public function getAsString(): string
    {
        $lines = [];
        reset($this->mapperConfig);
        foreach ($this->mapperConfig as $config => $configData) {
            if (false === isset($configData['snippet'])) {
                $lines[] = $config;
                continue;
            }

            $line = $config.'='.$configData['snippet'];
            if (count($configData['mappers']) > 0) {
                $mapper = [];
                foreach ($configData['mappers'] as $key => $mapperConfig) {
                    $mapperString = $mapperConfig['name'];
                    if (null !== $mapperConfig['arrayMapping']) {
                        $mapperString .= '{'.$mapperConfig['arrayMapping'].'}';
                    }
                    $varMapping = [];
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
        return array_keys($this->mapperConfig);
    }

    /**
     * {@inheritdoc}
     */
    public function getMappersForConfig($configname)
    {
        if (!array_key_exists($configname, $this->mapperConfig)) {
            return null;
        }

        $mappers = [];
        foreach ($this->mapperConfig[$configname]['mappers'] as $mapper) {
            $mappers[] = $mapper['name'];
        }
        reset($this->mapperConfig[$configname]['mappers']);

        return $mappers;
    }

    /**
     * {@inheritdoc}
     */
    public function getSnippetForConfig($configname)
    {
        if (!array_key_exists($configname, $this->mapperConfig)) {
            return null;
        }

        return $this->mapperConfig[$configname]['snippet'];
    }

    /**
     * {@inheritdoc}
     */
    public function getTransformationsForMapper($configname, $mappername)
    {
        if (!array_key_exists($configname, $this->mapperConfig)) {
            return null;
        }
        foreach ($this->mapperConfig[$configname]['mappers'] as $mapper) {
            if ($mappername === $mapper['name']) {
                reset($this->mapperConfig[$configname]['mappers']);

                return $mapper['varMapping'];
            }
        }
        reset($this->mapperConfig[$configname]['mappers']);

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getArrayMappingForMapper($configname, $mappername)
    {
        if (!array_key_exists($configname, $this->mapperConfig)) {
            return null;
        }

        foreach ($this->mapperConfig[$configname]['mappers'] as $mapper) {
            if ($mappername === $mapper['name']) {
                reset($this->mapperConfig[$configname]['mappers']);

                return $mapper['arrayMapping'];
            }
        }
        reset($this->mapperConfig[$configname]['mappers']);

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlainParsedConfig()
    {
        return $this->mapperConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigCount()
    {
        return count($this->mapperConfig);
    }

    /**
     * {@inheritdoc}
     */
    public function addMapper($config, $mapper, $placeAfterMapper = null)
    {
        if (false === isset($this->mapperConfig[$config])) {
            return false;
        }
        if (false === is_array($mapper)) {
            $mapper = [
                'arrayMapping' => null,
                'varMapping' => [],
                'name' => $mapper,
            ];
        }
        if (null === $placeAfterMapper) {
            $this->mapperConfig[$config]['mappers'][] = $mapper;
        } else {
            $bFound = false;
            $newMapperConfig = [];
            foreach ($this->mapperConfig[$config]['mappers'] as $key => $tmpMapperData) {
                $newMapperConfig[] = $tmpMapperData;
                if ($placeAfterMapper === $tmpMapperData['name']) {
                    $newMapperConfig[] = $mapper;
                    $bFound = true;
                }
            }
            reset($this->mapperConfig[$config]['mappers']);
            if (false === $bFound) {
                $newMapperConfig[] = $mapper;
            }
            $this->mapperConfig[$config]['mappers'] = $newMapperConfig;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function removeMapper($config, $mapper)
    {
        if (false === isset($this->mapperConfig[$config])) {
            return false;
        }

        $bFound = false;
        $newMapperConfig = [];
        foreach ($this->mapperConfig[$config]['mappers'] as $key => $tmpMapperData) {
            if ($mapper !== $tmpMapperData['name']) {
                $newMapperConfig[] = $tmpMapperData;
            } else {
                $bFound = true;
            }
        }
        reset($this->mapperConfig[$config]['mappers']);
        $this->mapperConfig[$config]['mappers'] = $newMapperConfig;

        return $bFound;
    }

    /**
     * {@inheritdoc}
     */
    public function replaceMapper($oldMapper, $newMapper, $config = null)
    {
        if (null !== $config && false === isset($this->mapperConfig[$config])) {
            return false;
        }

        $found = false;
        if (null === $config) {
            $configsToChange = $this->mapperConfig;
        } else {
            $configsToChange = [
                $config => $this->mapperConfig[$config],
            ];
        }
        foreach ($configsToChange as $configName => $configToChange) {
            $newMapperConfig = [];
            if (false === isset($configToChange['mappers'])) {
                continue;
            }
            foreach ($configToChange['mappers'] as $key => $originalMapperData) {
                if ($oldMapper === $originalMapperData['name']) {
                    $newMapperData = $originalMapperData;
                    $newMapperData['name'] = $newMapper;
                    $newMapperConfig[] = $newMapperData;
                    $found = true;
                } else {
                    $newMapperConfig[] = $originalMapperData;
                }
            }
            $this->mapperConfig[$configName]['mappers'] = $newMapperConfig;
        }

        return $found;
    }

    /**
     * {@inheritdoc}
     */
    public function changeSnippet($config, $newSnippet)
    {
        $this->mapperConfig[$config]['snippet'] = $newSnippet;
    }

    /**
     * {@inheritdoc}
     */
    public function addConfig($config, $snippetName, $mapperChain)
    {
        $this->mapperConfig[$config] = [
            'snippet' => $snippetName,
            'mappers' => [],
        ];
        if (false === is_array($mapperChain)) {
            $mapperChain = [$mapperChain];
        }
        /**
         * @var array $mapperChain
         */
        foreach ($mapperChain as $mapperName) {
            $this->mapperConfig[$config]['mappers'][] = [
                'arrayMapping' => null,
                'varMapping' => [],
                'name' => $mapperName,
            ];
        }
    }

    private function processConfigLine(string $line): void
    {
        $parts = explode('=', $line);
        $viewName = trim($parts[0]);
        $this->mapperConfig[$viewName] = [];

        if (1 === count($parts)) {
            return;
        }

        $this->parseViewConfig($viewName, trim($parts[1]));
    }

    private function parseViewConfig(string $viewName, string $config): void
    {
        $configParts = explode(';', $config);
        $this->mapperConfig[$viewName]['snippet'] = trim($configParts[0]);
        $this->mapperConfig[$viewName]['mappers'] = [];

        if (!isset($configParts[1])) {
            return;
        }

        $mappers = $this->cleanMappers(explode(',', trim($configParts[1])));

        foreach ($mappers as $key => $mapperConfig) {
            $this->mapperConfig[$viewName]['mappers'][$key] = $this->parseMapperConfig($mapperConfig);
        }
    }

    private function cleanMappers(array $mappers): array
    {
        return array_filter($mappers, static function ($mapper) {
            return '' !== trim($mapper);
        });
    }

    private function parseMapperConfig(string $mapperConfig): array
    {
        $mapperConfig = rtrim($mapperConfig, ',');
        $arrayMapping = null;
        $varMapping = [];

        $arrayPos = strpos($mapperConfig, '{');
        $varPos = strpos($mapperConfig, '[');

        if (false === $arrayPos && false === $varPos) {
            return [
                'name' => trim($mapperConfig),
                'arrayMapping' => $arrayMapping,
                'varMapping' => $varMapping,
            ];
        }

        $splitPos = min(false === $arrayPos ? PHP_INT_MAX : $arrayPos, false === $varPos ? PHP_INT_MAX : $varPos);
        $name = trim(substr($mapperConfig, 0, $splitPos));

        $varMapping = $this->extractMappings($mapperConfig, '/\[(.*?)\]/');
        $arrayMapping = $this->extractSingleMapping($mapperConfig, '/\{(.*?)\}/');

        return [
            'name' => $name,
            'arrayMapping' => $arrayMapping,
            'varMapping' => $varMapping,
        ];
    }

    private function extractMappings(string $config, string $pattern): array
    {
        preg_match_all($pattern, $config, $matches);
        $mappings = [];

        if (2 === count($matches)) {
            foreach ($matches[1] as $mapping) {
                $parts = explode('->', $mapping);
                $mappings[$parts[0]] = $parts[1];
            }
        }

        return $mappings;
    }

    private function extractSingleMapping(string $config, string $pattern): ?string
    {
        preg_match_all($pattern, $config, $matches);

        if (2 === count($matches) && !empty($matches[1])) {
            return $matches[1][0];
        }

        return null;
    }
}
