<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Service;

use ChameleonSystem\CoreBundle\DataAccess\DataAccessInterface;
use ChameleonSystem\CoreBundle\Util\RoutingUtilInterface;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use TdbCmsLanguage;
use TdbCmsPortal;
use TdbCmsPortalSystemPage;
use TdbCmsTree;

class SystemPageService implements SystemPageServiceInterface
{
    /**
     * @var PortalDomainServiceInterface
     */
    private $portalDomainService;
    /**
     * @var LanguageServiceInterface
     */
    private $languageService;
    /**
     * @var UrlUtil
     */
    private $urlUtil;
    /**
     * @var RoutingUtilInterface
     */
    private $routingUtil;
    /**
     * @var TreeServiceInterface
     */
    private $treeService;
    /**
     * @var DataAccessInterface
     */
    private $dataAccess;

    /**
     * @param PortalDomainServiceInterface $portalDomainService
     * @param LanguageServiceInterface     $languageService
     * @param UrlUtil                      $urlUtil
     * @param RoutingUtilInterface         $routingUtil
     * @param TreeServiceInterface         $treeNodeService
     * @param DataAccessInterface          $dataAccess
     */
    public function __construct(PortalDomainServiceInterface $portalDomainService, LanguageServiceInterface $languageService, UrlUtil $urlUtil, RoutingUtilInterface $routingUtil, TreeServiceInterface $treeNodeService, DataAccessInterface $dataAccess)
    {
        $this->portalDomainService = $portalDomainService;
        $this->languageService = $languageService;
        $this->urlUtil = $urlUtil;
        $this->routingUtil = $routingUtil;
        $this->treeService = $treeNodeService;
        $this->dataAccess = $dataAccess;
    }

    /**
     * {@inheritdoc}
     */
    public function getSystemPageList(TdbCmsPortal $portal, TdbCmsLanguage $language)
    {
        /** @var TdbCmsPortalSystemPage[] $systemPageList */
        $systemPageList = array();

        $pages = \TdbCmsPortalSystemPageList::GetList(null, $language->id);
        while ($page = &$pages->Next()) {
            /** @var TdbCmsPortalSystemPage $page */
            if ($page->fieldCmsPortalId === $portal->id) {
                $systemPageList[] = $page;
            }
        }

        return $systemPageList;
    }

    /**
     * {@inheritdoc}
     */
    public function getSystemPage($systemPageNameInternal, TdbCmsPortal $portal = null, TdbCmsLanguage $language = null)
    {
        if (null === $portal) {
            $portal = $this->portalDomainService->getActivePortal();
        }
        if (null === $portal) {
            return null;
        }
        if (null === $language) {
            $language = $this->languageService->getActiveLanguage();
        }

        $systemPageList = $this->dataAccess->loadAll($language->id);
        /** @var TdbCmsPortalSystemPage $systemPage */
        foreach ($systemPageList as $systemPage) {
            if ($systemPageNameInternal === $systemPage->fieldNameInternal
                && $portal->id === $systemPage->fieldCmsPortalId) {
                return $systemPage;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getLinkToSystemPageRelative($systemPageNameInternal, array $parameters = array(), TdbCmsPortal $portal = null, TdbCmsLanguage $language = null)
    {
        $tree = $this->getSystemPageTree($systemPageNameInternal, $portal, $language);

        return $this->treeService->getLinkToPageForTreeRelative($tree, $parameters, $language);
    }

    /**
     * @param string              $systemPageNameInternal
     * @param TdbCmsPortal|null   $portal
     * @param TdbCmsLanguage|null $language
     *
     * @return TdbCmsTree|null
     */
    private function getSystemPageTree($systemPageNameInternal, TdbCmsPortal $portal = null, TdbCmsLanguage $language = null)
    {
        $systemPage = $this->getSystemPage($systemPageNameInternal, $portal, $language);
        if (null === $systemPage) {
            throw new RouteNotFoundException("No system page was found with system name '$systemPageNameInternal'");
        }
        $tree = $this->treeService->getById($systemPage->fieldCmsTreeId);
        if (null === $tree) {
            throw new RouteNotFoundException("No tree node is assigned to the system page with system name '$systemPageNameInternal'");
        }

        return $tree;
    }

    /**
     * {@inheritdoc}
     */
    public function getLinkToSystemPageAbsolute($systemPageNameInternal, array $parameters = array(), TdbCmsPortal $portal = null, TdbCmsLanguage $language = null, $forceSecure = false)
    {
        $tree = $this->getSystemPageTree($systemPageNameInternal, $portal, $language);

        return $this->treeService->getLinkToPageForTreeAbsolute($tree, $parameters, $language);
    }
}
