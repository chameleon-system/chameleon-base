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
use TCMSPortal;
use TdbCmsLanguage;
use TdbCmsPortal;
use TdbCmsPortalDomains;
use TdbCmsPortalDomainsList;
use TdbCmsTree;

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

    /**
     * @param RoutingUtilDataAccessInterface $routingUtilDataAccess
     * @param TreeServiceInterface           $treeService
     * @param UrlPrefixGeneratorInterface    $urlPrefixGenerator
     */
    public function __construct(RoutingUtilDataAccessInterface $routingUtilDataAccess, TreeServiceInterface $treeService, UrlPrefixGeneratorInterface $urlPrefixGenerator)
    {
        $this->routingUtilDataAccess = $routingUtilDataAccess;
        $this->treeService = $treeService;
        $this->urlPrefixGenerator = $urlPrefixGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function getLinkForTreeNode(TdbCmsTree $tree, TdbCmsLanguage $language)
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
    public function getDomainRequirement(TdbCmsPortal $portal, TdbCmsLanguage $language, $secure)
    {
        $domainList = TdbCmsPortalDomainsList::GetListForCmsPortalId($portal->id, $language->id);
        $primaryDomain = null;
        $otherDomains = array();
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

        $domains = array();
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
     * @param TdbCmsPortalDomains $domain
     * @param bool                $secure
     *
     * @return string
     */
    private function getDomainNames(TdbCmsPortalDomains $domain, $secure)
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
    public function getAllPageRoutes(TdbCmsPortal $portal, TdbCmsLanguage $language)
    {
        /**
         * @var PagePath[] $routes
         */
        $routes = array();
        $homePagedef = $this->treeService->getById($portal->fieldHomeNodeId)->GetLinkedPage();
        $routes[$homePagedef] = new PagePath($homePagedef, '/');

        $pageAssignmentList = $this->routingUtilDataAccess->getAllPageAssignments($portal, $language);
        foreach ($pageAssignmentList as $treeId => $pageId) {
            if ($pageId === $homePagedef) {
                continue;
            }
            $path = $this->getPathForPageAssignment($treeId, $portal, $language);

            if (false === isset($routes[$pageId])) {
                $routes[$pageId] = new PagePath($pageId, $path);
            } else {
                $routes[$pageId]->addPath($path);
            }
        }

        return $routes;
    }

    /**
     * @param string       $treeId
     * @param TdbCmsPortal $portal
     *
     * @return string
     */
    private function getPathForPageAssignment($treeId, TdbCmsPortal $portal, TdbCmsLanguage $language)
    {
        $aStopNodes = TCMSPortal::GetStopNodes($portal->id);
        $pathNode = $this->treeService->getById($treeId, $language->id);
        $aPath = $pathNode->GetPath($aStopNodes);

        $pathParts = array();
        for ($i = 2; $i < $pathPartCount = count($aPath); ++$i) {
            $pathPart = $aPath[$i]->fieldUrlname;
            if (empty($pathPart)) {
                if ($portal->fieldShowNotTanslated) {
                    $pathPart = $aPath[$i]->sqlData['urlname'];
                } else {
                    $pathPart = '';
                }
            }
            $pathParts[] = $pathPart;
        }

        return $this->normalizeRoutePath(implode('/', $pathParts), $portal);
    }

    /**
     * {@inheritdoc}
     */
    public function normalizeRoutePath($path, TdbCmsPortal $portal)
    {
        if ('' === $path || '/' === $path) {
            return '/';
        }
        $normalizedPagePath = $path;
        $normalizedPagePath = rtrim($normalizedPagePath, '/');
        if ('.html' === mb_strtolower(mb_substr($normalizedPagePath, -5))) {
            $normalizedPagePath = mb_substr($normalizedPagePath, 0, -5);
        }
        if (CHAMELEON_SEO_URL_REWRITE_TO_LOWERCASE) {
            $normalizedPagePath = strtolower($path);
        }

        return $normalizedPagePath;
    }
}
