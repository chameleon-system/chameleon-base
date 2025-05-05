<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

interface TPkgViewRendererSnippetDirectoryInterface
{
    /**
     * @param bool $bWithDummyData
     * @param TdbCmsPortal|null $oPortal
     *
     * @return TPkgViewRendererSnippetDirectoryInterface[]
     */
    public function getDirTree($bWithDummyData = false, $oPortal = null);

    /**
     * @param TdbCmsPortal|null $oPortal
     * @param string|null $snippetPath
     *
     * @return string[]
     */
    public function getConfigTree($oPortal = null, $snippetPath = null);

    /**
     * @param array|TPkgViewRendererSnippetGalleryItem $aDirTree
     * @param string $sActiveRelativePath
     *
     * @return string[]
     */
    public function getSnippetList($aDirTree, $sActiveRelativePath);

    /**
     * Use this method to retrieve resources from snippet packages.
     * This is necessary, should you use an instance of ViewRenderer in your module's old style view.php
     * Here you have to include the resources of the package in your HTMLHeadIncludes by hand.
     *
     * @param string $sSnippetPath - the path to the snippet package
     *
     * @return array
     */
    public function getResourcesForSnippetPackage($sSnippetPath);

    /**
     * @param TdbCmsPortal|null $oPortal
     * @param string|null $sBaseDirectory
     *
     * @return string[]
     */
    public function getBasePathsFromInstance($oPortal = null, $sBaseDirectory = null);

    /**
     * @param TdbCmsPortal|null $oPortal
     * @param string|null $sBaseDirectory
     *
     * @return string[]
     */
    public function getBasePaths($oPortal = null, $sBaseDirectory = null);

    /**
     * @return string
     */
    public function getSnippetBaseDirectory();
}
