<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

interface IPkgCmsCoreLayoutPlugin
{
    /**
     * @param TModuleLoader $oModuleLoader
     */
    public function __construct(TModuleLoader $oModuleLoader);

    /**
     * generate the layout html.
     *
     * @param $contentIdentifier
     * @param $config
     */
    public function run($contentIdentifier, $config);
}
