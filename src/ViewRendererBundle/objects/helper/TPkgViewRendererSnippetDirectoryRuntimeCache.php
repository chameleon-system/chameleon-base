<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;

/**
 * TPkgViewRendererSnippetDirectoryRuntimeCache caches the results for `getBasePaths` and `getSnippetBaseDirectory`.
 *
 * `getBasePaths` in particular bases its runtime cache on the given/active portal.
 * A bundle extending the theme logic where the active theme depends on more than just the portal needs to implement
 * its own version of this decorator in order to cache properly.
 */
class TPkgViewRendererSnippetDirectoryRuntimeCache implements TPkgViewRendererSnippetDirectoryInterface
{
    /**
     * @var TPkgViewRendererSnippetDirectoryInterface
     */
    private $subject;
    /**
     * @var PortalDomainServiceInterface
     */
    private $portalDomainService;

    public function __construct(
        TPkgViewRendererSnippetDirectoryInterface $subject,
        PortalDomainServiceInterface $portalDomainService
    ) {
        $this->subject = $subject;
        $this->portalDomainService = $portalDomainService;
    }

    public function getDirTree($bWithDummyData = false, $oPortal = null)
    {
        return $this->subject->getDirTree($bWithDummyData, $oPortal);
    }

    public function getConfigTree($oPortal = null, $snippetPath = null)
    {
        return $this->subject->getConfigTree($oPortal, $snippetPath);
    }

    public function getSnippetList($aDirTree, $sActiveRelativePath)
    {
        return $this->subject->getSnippetList($aDirTree, $sActiveRelativePath);
    }

    public function getResourcesForSnippetPackage($sSnippetPath)
    {
        return $this->subject->getResourcesForSnippetPackage($sSnippetPath);
    }

    public function getBasePathsFromInstance($oPortal = null, $sBaseDirectory = null)
    {
        return $this->subject->getBasePathsFromInstance($oPortal, $sBaseDirectory);
    }

    public function getBasePaths($oPortal = null, $sBaseDirectory = null)
    {
        static $basePaths = [];
        if (null === $oPortal) {
            $oPortal = $this->portalDomainService->getActivePortal();
        }
        $portalCacheId = null === $oPortal ? '-1' : $oPortal->id;
        $portalCacheId .= $sBaseDirectory;
        if (isset($basePaths[$portalCacheId])) {
            return $basePaths[$portalCacheId];
        }

        $basePaths[$portalCacheId] = $this->subject->getBasePaths($oPortal, $sBaseDirectory);

        return $basePaths[$portalCacheId];
    }

    public function getSnippetBaseDirectory()
    {
        static $cachedDir = null;
        if (null !== $cachedDir) {
            return $cachedDir;
        }

        $cachedDir = $this->subject->getSnippetBaseDirectory();

        return $cachedDir;
    }
}
