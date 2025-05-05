<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Util;

use ChameleonSystem\CoreBundle\DataModel\Routing\PagePath;
use ChameleonSystem\CoreBundle\Service\TreeServiceInterface;

class RoutingUtil implements RoutingUtilInterface
{
    /**
     * @var RoutingUtilDataAccessInterface
     */
    private $routingUtilDataAccess;
    /**
     * @var TreeServiceInterface
     */
    private $treeService;
    /**
     * @var UrlPrefixGeneratorInterface
     */
    private $urlPrefixGenerator;

    public function __construct(RoutingUtilDataAccessInterface $routingUtilDataAccess, TreeServiceInterface $treeService, UrlPrefixGeneratorInterface $urlPrefixGenerator)
    {
        $this->routingUtilDataAccess = $routingUtilDataAccess;
        $this->treeService = $treeService;
        $this->urlPrefixGenerator = $urlPrefixGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function getLinkForTreeNode(\TdbCmsTree $tree, \TdbCmsLanguage $language)
    {
        $url = null;
        $linkedPage = $tree->GetLinkedPageObject();
        if (false === $linkedPage) {
            return null;
        }
        $portal = $linkedPage->GetPortal();
        if (null === $portal) {
            return null;
        }

        $routes = $this->getAllPageRoutes($portal, $language);
        $url = $routes[$linkedPage->id]->getPrimaryPath();
        $url = $tree->replacePlaceHolderInURL($url);
        if (0 !== strpos($url, '/')) {
            $url = '/'.$url;
        }
        $url = $this->urlPrefixGenerator->generatePrefix($portal, $language).$url;

        return $url;
    }

    /**
     * @return string
     */
    public function getHostRequirementPlaceholder()
    {
        return 'domain';
    }

    /**
     * {@inheritdoc}
     */
    public function getDomainRequirement(\TdbCmsPortal $portal, \TdbCmsLanguage $language, $secure)
    {
        $domainList = \TdbCmsPortalDomainsList::GetListForCmsPortalId($portal->id, $language->id);
        $primaryDomain = null;
        $otherDomains = [];
        while ($domain = $domainList->Next()) {
            if (!empty($domain->fieldCmsLanguageId) && $domain->fieldCmsLanguageId !== $language->id) {
                continue;
            }
            if ($domain->fieldIsMasterDomain) {
                $primaryDomain = $domain;
            } else {
                $otherDomains[] = $domain;
            }
        }

        $domains = [];
        if (null !== $primaryDomain) {
            $domainNames = $this->getDomainNames($primaryDomain, $secure);
            if ('' !== $domainNames) {
                $domains[] = $domainNames;
            }
        }
        foreach ($otherDomains as $domain) {
            $domainNames = $this->getDomainNames($domain, $secure);
            if ('' !== $domainNames) {
                $domains[] = $domainNames;
            }
        }

        return implode('|', $domains);
    }

    /**
     * @param bool $secure
     *
     * @return string
     */
    private function getDomainNames(\TdbCmsPortalDomains $domain, $secure)
    {
        if (!empty($domain->fieldSslname)) {
            if ($secure) {
                return $domain->fieldSslname;
            }

            if ($domain->fieldSslname !== $domain->fieldName) {
                return $domain->fieldSslname.'|'.$domain->fieldName;
            }
        }

        return $domain->fieldName;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllPageRoutes(\TdbCmsPortal $portal, \TdbCmsLanguage $language)
    {
        /**
         * @var PagePath[] $routes
         */
        $routes = [];

        /** @var string $homePagedef */
        $homePagedef = $this->treeService->getById($portal->fieldHomeNodeId)->GetLinkedPage();
        $routes[$homePagedef] = new PagePath($homePagedef, '/');

        $pageAssignmentList = $this->routingUtilDataAccess->getAllPageAssignments($portal, $language);
        foreach ($pageAssignmentList as $treeId => $pageId) {
            if ($pageId === $homePagedef) {
                continue;
            }
            $path = $this->getPathForPageAssignment($treeId, $portal, $language);
            if (null === $path) {
                continue;
            }

            if (false === isset($routes[$pageId])) {
                $routes[$pageId] = new PagePath($pageId, $path);
            } else {
                $routes[$pageId]->addPath($path);
            }
        }

        return $routes;
    }

    private function getPathForPageAssignment(string $treeId, \TdbCmsPortal $portal, \TdbCmsLanguage $language): ?string
    {
        $pathNode = $this->treeService->getById($treeId, $language->id);
        if (null === $pathNode) {
            return null;
        }

        $aPath = $pathNode->GetPath(\TCMSPortal::GetStopNodes($portal->id));

        $navigationRootDepth = $this->getNavigationRootDepth($portal, $aPath);
        /*
         * If the tree node is not part of any navigation, it is not accessible --> abort.
         */
        if (null === $navigationRootDepth) {
            return null;
        }

        $pathParts = [];
        for ($i = $navigationRootDepth + 1; $i < $pathPartCount = \count($aPath); ++$i) {
            $node = $aPath[$i];
            $pathPart = $node->fieldUrlname;

            if (empty($pathPart)) {
                if ($portal->fieldShowNotTanslated) {
                    $pathPart = $node->sqlData['urlname'];
                } else {
                    $pathPart = '';
                }
            }
            $pathParts[] = $pathPart;
        }

        return $this->normalizeRoutePath(implode('/', $pathParts), $portal);
    }

    /**
     * Returns the depth of the first node in $pathElements that is also registered as navigation root for $portal (0-based).
     *
     * @param \TdbCmsTree[] $pathElements
     *
     * @return string|null the depth of a navigation root node, or null if $pathElements does not contain a navigation
     *                     root node
     *
     * @psalm-return numeric-string
     */
    private function getNavigationRootDepth(\TdbCmsPortal $portal, array $pathElements): ?string
    {
        $naviNodeIdList = $portal->GetNaviNodeIds();
        foreach ($pathElements as $i => $node) {
            if (true === \in_array($node->id, $naviNodeIdList, true)) {
                return $i;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function normalizeRoutePath($path, \TdbCmsPortal $portal)
    {
        if ('' === $path || '/' === $path) {
            return '/';
        }
        $normalizedPagePath = $path;
        $normalizedPagePath = \rtrim($normalizedPagePath, '/');
        if ('.html' === mb_strtolower(\mb_substr($normalizedPagePath, -5))) {
            $normalizedPagePath = \mb_substr($normalizedPagePath, 0, -5);
        }
        if (CHAMELEON_SEO_URL_REWRITE_TO_LOWERCASE) {
            $normalizedPagePath = \strtolower($normalizedPagePath);
        }

        return $normalizedPagePath;
    }
}
