<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgCmsStringUtilities_ReadConfig
{
    /**
     * @var string
     */
    private $sConfig;

    /**
     * @var array|null
     */
    private $aConfig;

    /**
     * config params are separated by \n. Key from value by "=".
     *
     * @param string $sString
     */
    public function __construct($sString)
    {
        $this->sConfig = $sString;
        $this->aConfig = $this->convertToArray($sString);
    }

    /**
     * @param string $sKey
     *
     * @return string|null
     */
    public function getConfigValue($sKey)
    {
        if (true === isset($this->aConfig[$sKey])) {
            return $this->aConfig[$sKey];
        }

        return null;
    }

    /**
     * return complete config as array.
     *
     * @return array|null
     */
    public function getConfigArray()
    {
        return $this->aConfig;
    }

    /**
     * Get all config parameter as array.
     *
     * @param string $configString
     *
     * @return array
     */
    private function convertToArray($configString)
    {
        $aConfigParameters = [];
        if (!empty($configString)) {
            $aConfigRows = explode("\n", $configString);
            foreach ($aConfigRows as $row) {
                $sepPos = strpos($row, '=');
                if (false !== $sepPos) {
                    $tmpKey = substr($row, 0, $sepPos);
                    $tmpVal = substr($row, $sepPos + 1);
                    $aConfigParameters[trim($tmpKey)] = trim($tmpVal);
                }
            }
        }

        return $aConfigParameters;
    }
}
