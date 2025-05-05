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
use ChameleonSystem\CoreBundle\Routing\PortalAndLanguageAwareRouterInterface;
use ChameleonSystem\CoreBundle\Util\PageServiceUtilInterface;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PageService implements PageServiceInterface
{
    /**
     * @var PortalAndLanguageAwareRouterInterface
     */
    private $router;
    /**
     * @var DataAccessInterface
     */
    private $dataAccess;
    /**
     * @var TreeNodeServiceInterface
     */
    private $treeNodeService;
    /**
     * @var LanguageServiceInterface
     */
    private $languageService;
    /**
     * @var PortalDomainServiceInterface
     */
    private $portalDomainService;
    /**
     * @var PageServiceUtilInterface
     */
    private $pageServiceUtil;
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var UrlUtil
     */
    private $urlUtil;

    public function __construct(PortalAndLanguageAwareRouterInterface $router, DataAccessInterface $dataAccess, TreeNodeServiceInterface $treeNodeService, LanguageServiceInterface $languageService, PortalDomainServiceInterface $portalDomainService, PageServiceUtilInterface $pageServiceUtil, RequestStack $requestStack, UrlUtil $urlUtil)
    {
        $this->router = $router;
        $this->dataAccess = $dataAccess;
        $this->treeNodeService = $treeNodeService;
        $this->languageService = $languageService;
        $this->portalDomainService = $portalDomainService;
        $this->pageServiceUtil = $pageServiceUtil;
        $this->requestStack = $requestStack;
        $this->urlUtil = $urlUtil;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($pageId, $languageId = null)
    {
        $pageList = $this->dataAccess->loadAll($languageId);
        if (!isset($pageList[$pageId])) {
            return null;
        }

        return $pageList[$pageId];
    }

    /**
     * {@inheritdoc}
     */
    public function getByTreeId($treeId, $languageId = null)
    {
        $treeNode = $this->treeNodeService->getByTreeId($treeId, $languageId);
        if (null === $treeNode) {
            return null;
        }

        return $this->getById($treeNode->fieldContid);
    }

    /**
     * {@inheritdoc}
     */
    public function getLinkToPageRelative($pageId, array $parameters = [], ?\TdbCmsLanguage $language = null)
    {
        $page = $this->getById($pageId);
        if (null === $page) {
            throw new RouteNotFoundException('No page found with ID '.$pageId);
        }

        return $this->getLinkToPageObject($page, $parameters, $language, false, UrlGeneratorInterface::ABSOLUTE_PATH);
    }

    /**
     * {@inheritdoc}
     */
    public function getLinkToPageObjectRelative(\TdbCmsTplPage $page, array $parameters = [], ?\TdbCmsLanguage $language = null)
    {
        return $this->getLinkToPageObject($page, $parameters, $language, false, UrlGeneratorInterface::ABSOLUTE_PATH);
    }

    /**
     * @param bool $forceSecure
     * @param int $referenceType
     *
     * @psalm-param UrlGeneratorInterface::* $referenceType
     *
     * @return string
     *
     * @throws RouteNotFoundException
     */
    private function getLinkToPageObject(\TdbCmsTplPage $page, array $parameters = [], ?\TdbCmsLanguage $language = null, $forceSecure = false, $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        $portal = $page->GetPortal();
        if ($page->GetMainTreeId() === $portal->fieldHomeNodeId) {
            return $this->getLinkToPortalHomePage($parameters, $portal, $language, $forceSecure, $referenceType);
        }
        if (null === $language) {
            $language = $this->getLanguageFallback($page);
            if (null === $language) {
                throw new RouteNotFoundException('No language given and no active language could be retrieved.');
            }
        }
        $parameters['pagePath'] = $this->pageServiceUtil->getPagePath($page, $language);

        $url = $this->router->generateWithPrefixes('cms_tpl_page', $parameters, $portal, $language, $referenceType);

        $request = $this->requestStack->getCurrentRequest();
        if (true === $page->fieldUsessl && null !== $request && false === $request->isSecure()) {
            $url = $this->urlUtil->getAbsoluteUrl($url, true, null, $portal, $language);
        }

        return $this->pageServiceUtil->postProcessUrl($url, $portal, $language, $forceSecure);
    }

    /**
     * @return \TdbCmsLanguage|null
     */
    private function getLanguageFallback(\TdbCmsTplPage $page)
    {
        $language = $this->languageService->getActiveLanguage();
        if (null === $language) {
            $languageId = $page->GetLanguageID();
            if (null !== $languageId) {
                $language = $this->languageService->getLanguage($languageId);
            }
        }

        return $language;
    }

    /**
     * {@inheritdoc}
     */
    public function getLinkToPageAbsolute($pageId, array $parameters = [], ?\TdbCmsLanguage $language = null, $forceSecure = false)
    {
        $page = $this->getById($pageId);
        if (null === $page) {
            throw new RouteNotFoundException('No page found with ID '.$pageId);
        }

        return $this->getLinkToPageObject($page, $parameters, $language, $forceSecure, UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * {@inheritdoc}
     */
    public function getLinkToPageObjectAbsolute(\TdbCmsTplPage $page, array $parameters = [], ?\TdbCmsLanguage $language = null, $forceSecure = false)
    {
        return $this->getLinkToPageObject($page, $parameters, $language, $forceSecure, UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * {@inheritdoc}
     */
    public function getLinkToPortalHomePageRelative(array $parameters = [], ?\TdbCmsPortal $portal = null, ?\TdbCmsLanguage $language = null)
    {
        return $this->getLinkToPortalHomePage($parameters, $portal, $language, false, UrlGeneratorInterface::ABSOLUTE_PATH);
    }

    /**
     * {@inheritdoc}
     */
    public function getLinkToPortalHomePageAbsolute(array $parameters = [], ?\TdbCmsPortal $portal = null, ?\TdbCmsLanguage $language = null, $forceSecure = false)
    {
        return $this->getLinkToPortalHomePage($parameters, $portal, $language, $forceSecure, UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * @param bool $forceSecure
     * @param int $referenceType
     *
     * @psalm-param UrlGeneratorInterface::* $referenceType
     *
     * @return string
     *
     * @throws RouteNotFoundException
     */
    private function getLinkToPortalHomePage(
        array $parameters = [],
        ?\TdbCmsPortal $portal = null,
        ?\TdbCmsLanguage $language = null,
        $forceSecure = false,
        $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH
    ) {
        if (null === $portal) {
            $portal = $this->portalDomainService->getActivePortal();
            if (null === $portal) {
                throw new RouteNotFoundException('No portal given and no active portal could be retrieved.');
            }
        }
        if (null === $language) {
            $portalHomeNode = $this->getByTreeId($portal->fieldHomeNodeId);
            if (null === $portalHomeNode) {
                throw new RouteNotFoundException(sprintf('Home node for portal with ID %s could not be found.', $portal->id));
            }
            $language = $this->getLanguageFallback($portalHomeNode);
            if (null === $language) {
                throw new RouteNotFoundException('No language given and no active language could be retrieved.');
            }
        }

        $url = $this->router->generateWithPrefixes('cms_tpl_page_home', $parameters, $portal, $language, $referenceType);

        return $this->pageServiceUtil->postProcessUrl($url, $portal, $language, $forceSecure);
    }
}
