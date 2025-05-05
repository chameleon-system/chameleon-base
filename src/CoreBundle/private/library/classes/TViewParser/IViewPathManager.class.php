<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

interface IViewPathManager
{
    /**
     * Get the path to a module view from framework.
     *
     * @param string $sModuleName
     * @param string $sViewName
     *
     * @return string
     */
    public function getModuleViewPath($sModuleName, $sViewName);

    /**
     * @param string $sViewName
     * @param string $sSubType
     * @param string $sType
     *
     * @return string
     */
    public function getObjectViewPath($sViewName, $sSubType = '', $sType = 'Core');

    /**
     * @param string $sViewName
     * @param string $sModuleName
     * @param string $sType
     *
     * @return string
     */
    public function getBackendModuleViewPath($sViewName, $sModuleName = '', $sType = 'Core');

    /**
     * @param string $sViewName
     * @param string $sModuleName
     * @param string $sType
     * @param string|null $sMappedPath - if there is already a mapped path replacing PATH_CUSTOMER_FRAMEWORK
     *
     * @return string
     */
    public function getWebModuleViewPath($sViewName, $sModuleName, $sType = 'Customer', $sMappedPath = null);

    /**
     * @param string $sViewName
     * @param string $sSubType
     * @param string $sType
     *
     * @return string
     */
    public function getObjectPackageViewPath($sViewName, $sSubType = '', $sType = 'Core');

    /**
     * @param string $sFullView
     *
     * @return string
     */
    public function getBackendModuleViewFromFullPath($sFullView);

    /**
     * get path to layout file if exists in theme directory chain or in private/framework/layoutTemplates.
     *
     * @param string $sLayoutName name of layout with or without .layout.php
     *
     * @return string|null
     */
    public function getLayoutViewPath($sLayoutName);
}
