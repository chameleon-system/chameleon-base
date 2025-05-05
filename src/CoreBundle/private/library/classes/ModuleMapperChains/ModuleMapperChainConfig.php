<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class ModuleMapperChainConfig implements ModuleMapperChainConfigInterface
{
    /**
     * @var array
     */
    private $mapperChains = [];

    /**
     * {@inheritdoc}
     */
    public function loadFromString($configString)
    {
        $configString = trim($configString);
        if ('' === $configString) {
            return;
        }

        $lines = explode("\n", $configString);
        foreach ($lines as $line) {
            $chain = $this->getChainFromLine($line);
            if (null !== $chain) {
                $this->mapperChains = array_merge($this->mapperChains, $chain);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getMapperChains()
    {
        return $this->mapperChains;
    }

    /**
     * @param string $line
     *
     * @return array|null
     *
     * @throws ErrorException
     */
    private function getChainFromLine($line)
    {
        $line = str_replace([' ', "\r"], '', $line);
        if ('' === $line) {
            return null;
        }

        $aliasIdent = strpos($line, '=');
        if (false === $aliasIdent) {
            throw new ErrorException("Invalid pattern [{$line}] for mapper chain: Missing '='.", 0, E_USER_WARNING, __FILE__, __LINE__);
        }
        $alias = substr($line, 0, $aliasIdent);
        $mapperString = substr($line, $aliasIdent + 1);
        $rawMapperList = explode(',', $mapperString);

        $mapperList = [];
        foreach ($rawMapperList as $mapper) {
            if ('' === $mapper) {
                continue;
            }

            $mapperList[] = $mapper;
        }

        if (0 === count($mapperList)) {
            throw new ErrorException("Invalid pattern [{$line}] for mapper chain: Alias {$alias} has no mappers.", 0, E_USER_WARNING, __FILE__, __LINE__);
        }

        return [$alias => $mapperList];
    }

    /**
     * {@inheritdoc}
     */
    public function getAsString()
    {
        $mapperChains = [];
        foreach ($this->mapperChains as $mapperName => $mapperChain) {
            $tmp = $this->getMapperChainAsString($mapperName, $mapperChain);
            if (null === $tmp) {
                continue;
            }
            $mapperChains[] = $tmp;
        }

        return implode("\n", $mapperChains);
    }

    /**
     * @param string $mapperName
     * @param array $mapperChain
     *
     * @return string|null
     */
    private function getMapperChainAsString($mapperName, $mapperChain)
    {
        if (0 === count($mapperChain)) {
            return null;
        }

        return $mapperName.' = '.implode(', ', $mapperChain);
    }

    /**
     * {@inheritdoc}
     */
    public function addMapperToChain($mapperChainName, $newMapper, $positionAfter = null)
    {
        if (false === isset($this->mapperChains[$mapperChainName])) {
            throw new ErrorException("Failed adding mapper {$newMapper} because the mapper chain {$mapperChainName} does not exist.");
        }
        if (null === $positionAfter) {
            $this->mapperChains[$mapperChainName][] = $newMapper;

            return;
        }

        $mapperChain = $this->mapperChains[$mapperChainName];
        $positionAfterIndex = array_search($positionAfter, $mapperChain);
        if (false === $positionAfterIndex) {
            throw new ErrorException("Unable to position {$newMapper} in chain {$mapperChainName} after {$positionAfter} because {$positionAfter} was not found.");
        }

        array_splice($mapperChain, $positionAfterIndex + 1, 0, [$newMapper]);
        $this->mapperChains[$mapperChainName] = $mapperChain;
    }

    /**
     * {@inheritdoc}
     */
    public function removeMapperFromMapperChain($mapperChainName, $mapperName)
    {
        if (false === isset($this->mapperChains[$mapperChainName])) {
            throw new ErrorException("Failed to remove chain {$mapperChainName} because it does not exist in the list. The following chains exist: ".implode(', ', array_keys($this->mapperChains)));
        }

        $chainPosition = array_search($mapperName, $this->mapperChains[$mapperChainName]);
        if (false === $chainPosition) {
            throw new ErrorException("Failed to remove {$mapperName} from chain {$mapperChainName} because it does not exist in the list. The following mappers exist in the chain: ".implode(', ', $this->mapperChains[$mapperChainName]));
        }

        array_splice($this->mapperChains[$mapperChainName], $chainPosition, 1);
    }

    /**
     * @param string $oldMapperName
     * @param string $newMapperName
     * @param string|null $mapperChainName
     *
     * @return bool
     *
     * @throws ErrorException
     */
    public function replaceMapper($oldMapperName, $newMapperName, $mapperChainName = null)
    {
        if (null !== $mapperChainName && false === isset($this->mapperChains[$mapperChainName])) {
            throw new ErrorException("Failed to replace mapper in chain {$mapperChainName} because it does not exist in the list. The following chains exist: ".implode(', ', array_keys($this->mapperChains)));
        }

        $found = false;
        if (null === $mapperChainName) {
            $mapperChainsToChange = $this->mapperChains;
        } else {
            $mapperChainsToChange = [
                $mapperChainName => $this->mapperChains[$mapperChainName],
            ];
        }
        foreach ($mapperChainsToChange as $name => $mapperChainToChange) {
            $newMapperChain = [];
            foreach ($mapperChainToChange as $originalMapperName) {
                if ($oldMapperName === $originalMapperName) {
                    $newMapperChain[] = $newMapperName;
                    $found = true;
                } else {
                    $newMapperChain[] = $originalMapperName;
                }
            }
            $this->mapperChains[$name] = $newMapperChain;
        }

        return $found;
    }

    /**
     * {@inheritdoc}
     */
    public function addMapperChain($mapperChainName, array $mapperList)
    {
        if (true === isset($this->mapperChains[$mapperChainName])) {
            throw new ErrorException("Failed to add chain {$mapperChainName} because it already exists in the list. The following chains exist: ".implode(', ', array_keys($this->mapperChains)));
        }
        if (0 === count($mapperList)) {
            throw new ErrorException("Failed to add chain {$mapperChainName} because you provided no mappers for that chain. You provided: ".print_r($mapperList, true));
        }
        $this->mapperChains[$mapperChainName] = $mapperList;
    }

    /**
     * {@inheritdoc}
     */
    public function removeMapperChain($mapperChainName)
    {
        if (false === isset($this->mapperChains[$mapperChainName])) {
            throw new ErrorException("Failed to remove chain {$mapperChainName} because it does not exist in the list. The following chains exist: ".implode(', ', array_keys($this->mapperChains)));
        }

        unset($this->mapperChains[$mapperChainName]);
    }
}
