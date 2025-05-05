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
    public function __construct(TModuleLoader $oModuleLoader);

    /**
     * generate the layout html.
     *
     * @param string $contentIdentifier
     * @param array<string, mixed> $config
     *
     * @return void
     */
    public function run($contentIdentifier, $config);
}
