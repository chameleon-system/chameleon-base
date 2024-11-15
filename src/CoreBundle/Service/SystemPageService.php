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
use ChameleonSystem\CoreBundle\DataModel\PageDataModel;
use ChameleonSystem\CoreBundle\Util\RoutingUtilInterface;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class SystemPageService implements SystemPageServiceInterface
{
    private PortalDomainServiceInterface $portalDomainService;
    private LanguageServiceInterface $languageService;
    private UrlUtil $urlUtil;
    private RoutingUtilInterface $routingUtil;
    private TreeServiceInterface $treeService;
    private DataAccessInterface $dataAccess;
    private ActivePageServiceInterface $activePageService;

    /**
     * \TdbCmsPortalSystemPage[] $systemPageCache
     */
    private array $systemPageCache = [];

    /**
     * \TdbCmsTree[] $systemPageTreeCache
     */
    private array $systemPageTreeCache = [];

    public function __construct(
        PortalDomainServiceInterface $portalDomainService,
        LanguageServiceInterface $languageService,
        UrlUtil $urlUtil,
        RoutingUtilInterface $routingUtil,
        TreeServiceInterface $treeNodeService,
        DataAccessInterface $dataAccess,
        ActivePageServiceInterface $activePageService)
    {
        $this->portalDomainService = $portalDomainService;
        $this->languageService = $languageService;
        $this->urlUtil = $urlUtil;
        $this->routingUtil = $routingUtil;
        $this->treeService = $treeNodeService;
        $this->dataAccess = $dataAccess;
        $this->activePageService = $activePageService;
    }

    public function getSystemPageList(\TdbCmsPortal $portal, \TdbCmsLanguage $language)
    {
        /** @var \TdbCmsPortalSystemPage[] $systemPageList */
        $systemPageList = [];

        $pages = \TdbCmsPortalSystemPageList::GetList(null, $language->id);
        while ($page = &$pages->Next()) {
            /** @var \TdbCmsPortalSystemPage $page */
            if ($page->fieldCmsPortalId === $portal->id) {
                $systemPageList[] = $page;
            }
        }

        return $systemPageList;
    }

    public function getSystemPage($systemPageNameInternal, ?\TdbCmsPortal $portal = null, ?\TdbCmsLanguage $language = null)
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

        if (null === $language) {
            $language = $this->languageService->getCmsBaseLanguage();
        }

        $systemPageList = $this->dataAccess->loadAll($language->id);
        /** @var \TdbCmsPortalSystemPage $systemPage */
        foreach ($systemPageList as $systemPage) {
            if ($systemPageNameInternal === $systemPage->fieldNameInternal
                && $portal->id === $systemPage->fieldCmsPortalId) {
                return $systemPage;
            }
        }

        return null;
    }

    public function getLinkToSystemPageRelative($systemPageNameInternal, array $parameters = [], ?\TdbCmsPortal $portal = null, ?\TdbCmsLanguage $language = null)
    {
        $tree = $this->getSystemPageTree($systemPageNameInternal, $portal, $language);

        return $this->treeService->getLinkToPageForTreeRelative($tree, $parameters, $language);
    }

    public function getSystemPageTree(string $systemPageNameInternal, ?\TdbCmsPortal $portal = null, ?\TdbCmsLanguage $language = null): ?\TdbCmsTree
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

    public function getLinkToSystemPageAbsolute($systemPageNameInternal, array $parameters = [], ?\TdbCmsPortal $portal = null, ?\TdbCmsLanguage $language = null, $forceSecure = false)
    {
        $tree = $this->getSystemPageTree($systemPageNameInternal, $portal, $language);

        return $this->treeService->getLinkToPageForTreeAbsolute($tree, $parameters, $language);
    }

    public function getPageDataModel(string $systemPageNameInternal, array $parameters = [], ?\TdbCmsPortal $portal = null, ?\TdbCmsLanguage $language = null): ?PageDataModel
    {
        $relativeUrl = $this->getLinkToSystemPageRelative($systemPageNameInternal, $parameters, $portal, $language);
        $absoluteUrl = $this->getLinkToSystemPageAbsolute($systemPageNameInternal, $parameters, $portal, $language);

        $treeNodeRecord = $this->getSystemPageTree($systemPageNameInternal, $portal, $language);
        $pageRecord = $treeNodeRecord->GetLinkedPageObject();

        $pageDataModel = new PageDataModel();
        $pageDataModel->setPageId($pageRecord->id);
        $pageDataModel->setPortalId($pageRecord->fieldCmsPortalId);
        $pageDataModel->setName($pageRecord->GetName());
        $pageDataModel->setPrimarytreeNodeId($treeNodeRecord->id);
        $pageDataModel->setRelativeUrl($relativeUrl);
        $pageDataModel->setAbsoluteUrl($absoluteUrl);
        $pageDataModel->setIsActivePage($this->isActivePage($pageRecord));

        return $pageDataModel;
    }

    protected function isActivePage(\TdbCmsTplPage $pageRecord)
    {
        $activePage = $this->activePageService->getActivePage();

        if (null === $activePage) {
            return false;
        }

        return $pageRecord->id = $activePage->id;
    }
}
