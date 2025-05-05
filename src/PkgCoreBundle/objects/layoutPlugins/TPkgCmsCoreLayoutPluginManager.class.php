<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgCmsCoreLayoutPluginManager
{
    /**
     * @var TModuleLoader|null
     */
    private $moduleLoader;

    public function __construct(TModuleLoader $moduleLoader)
    {
        $this->moduleLoader = $moduleLoader;
    }

    /**
     * @param string $pluginClassName
     * @param string $contentIdentifier
     * @param array $config
     *
     * @return void
     */
    public function includePlugin($pluginClassName, $contentIdentifier, $config = [])
    {
        /** @var IPkgCmsCoreLayoutPlugin $oPlugin */
        $oPlugin = new $pluginClassName($this->moduleLoader);
        $oPlugin->run($contentIdentifier, $config);
    }
}
