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

use ChameleonSystem\CoreBundle\CoreEvents;
use ChameleonSystem\CoreBundle\Event\ChangeActivePageEvent;
use ChameleonSystem\CoreBundle\Service\Initializer\ActivePageServiceInitializerInterface;
use ChameleonSystem\CoreBundle\Util\RoutingUtilInterface;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use TCMSActivePage;
use TdbCmsLanguage;

/**
 * Class ActivePageService.
 */
class ActivePageService implements ActivePageServiceInterface
{
    /**
     * @var LanguageServiceInterface
     */
    private $languageService;
    /**
     * @var ActivePageServiceInitializerInterface
     */
    private $activePageServiceInitializer;
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var TCMSActivePage
     */
    private $activePage;
    /**
     * @var UrlUtil
     */
    private $urlUtil;
    /**
     * @var RequestInfoServiceInterface
     */
    private $requestInfoService;
    /**
     * @var RoutingUtilInterface
     */
    private $routingUtil;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param LanguageServiceInterface              $languageService
     * @param ActivePageServiceInitializerInterface $activePageServiceInitializer
     * @param RequestStack                          $requestStack
     * @param RouterInterface                       $router
     * @param UrlUtil                               $urlUtil
     * @param RequestInfoServiceInterface           $requestInfoService
     * @param RoutingUtilInterface                  $routingUtil
     * @param EventDispatcherInterface              $eventDispatcher
     */
    public function __construct(LanguageServiceInterface $languageService, ActivePageServiceInitializerInterface $activePageServiceInitializer, RequestStack $requestStack, RouterInterface $router, UrlUtil $urlUtil, RequestInfoServiceInterface $requestInfoService, RoutingUtilInterface $routingUtil, EventDispatcherInterface $eventDispatcher)
    {
        $this->languageService = $languageService;
        $this->activePageServiceInitializer = $activePageServiceInitializer;
        $this->requestStack = $requestStack;
        $this->router = $router;
        $this->urlUtil = $urlUtil;
        $this->requestInfoService = $requestInfoService;
        $this->routingUtil = $routingUtil;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function getActivePage($reload = false)
    {
        if (null === $this->activePage || $reload) {
            $this->initialize();
        }

        return $this->activePage;
    }

    private function initialize()
    {
        $this->activePageServiceInitializer->initialize($this);
    }

    /**
     * {@inheritdoc}
     */
    public function setActivePage($activePageId, $referrerPageId)
    {
        if (null === $activePageId || '' === $activePageId) {
            return;
        }
        $activePage = new TCMSActivePage();
        if (false === $activePage->Load($activePageId)) {
            // try the referrer
            if (null === $referrerPageId || '' === $referrerPageId) {
                return;
            }
            if (false === $activePage->Load($referrerPageId)) {
                return;
            }
        }
        $oldActivePage = $this->activePage;
        $this->activePage = $activePage;

        if ($this->activePage !== $oldActivePage) {
            $event = new ChangeActivePageEvent($this->activePage, $oldActivePage);
            $this->eventDispatcher->dispatch(CoreEvents::CHANGE_ACTIVE_PAGE, $event);
        }

        $languageOfPage = $activePage->GetLanguageID();
        if (null !== $languageOfPage) {
            $this->languageService->setActiveLanguage($languageOfPage);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getLinkToActivePageRelative(array $additionalParameters = array(), array $excludeParameters = array(), TdbCmsLanguage $language = null)
    {
        return $this->getLinkToActivePage($additionalParameters, $excludeParameters, $language, UrlGeneratorInterface::ABSOLUTE_PATH);
    }

    /**
     * @param array               $additionalParameters
     * @param array               $excludeParameters
     * @param TdbCmsLanguage|null $language
     * @param bool|string         $referenceType
     * @param bool                $forceSecure
     *
     * @return string
     */
    private function getLinkToActivePage(array $additionalParameters = array(), array $excludeParameters = array(), TdbCmsLanguage $language = null, $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH, $forceSecure = false)
    {
        $request = $this->requestStack->getMasterRequest();
        $route = $request->attributes->get('_route');
        if (null === $route) {
            return '';
        }
        $finalParameterList = $this->getFinalParameterList($request, $additionalParameters, $excludeParameters, $language);
        /*
         * The cms_pagedef is a manual pseudo-route defined in the ChameleonFrontendRouter for routes that only consist
         * of a pagedef parameter.
         */
        if ('cms_pagedef' === $route) {
            return $this->urlUtil->getArrayAsUrl($finalParameterList, PATH_CUSTOMER_FRAMEWORK_CONTROLLER.'?', '&');
        }
        /*
         * The link may not contain a pagedef parameter. It is intended to solve this the "right" way by not adding
         * the parameter to the query parameters in the first place (ticket #30278), but as long as the request contains
         * this parameter, we need to deal with it manually.
         */
        if (isset($finalParameterList['pagedef'])
            && !$this->requestInfoService->isBackendMode()) {
            unset($finalParameterList['pagedef']);
        }

        /*
         * Routes have the form "routeName" or "routeName-portalId-languageIso"
         */
        if (null !== $language && '-' === substr($route, -3, 1)) {
            $route = substr($route, 0, -2).$language->fieldIso6391;
        }

        return $this->router->generate($route, $finalParameterList, $referenceType);
    }

    /**
     * Mixes different sources of parameters for the resulting URL. The current route parameters and request parameters
     * will be added as long as they are not contained in the $excludeParameters list. Afterwards, the $additionalParameters
     * will always be added.
     *
     * @param Request $request
     * @param array   $additionalParameters
     * @param array   $excludeParameters
     *
     * @return array
     */
    private function getFinalParameterList(Request $request, array $additionalParameters, array $excludeParameters, TdbCmsLanguage $language = null)
    {
        $parameters = array();
        if ($request->attributes->has('_route_params')) {
            $parameters = array_merge($parameters, $request->attributes->get('_route_params'));
            $this->modifyRouteParameters($parameters, $language);
        }
        $parameters = array_merge($request->query->all(), $parameters);
        foreach ($excludeParameters as $arrayName => $exclude) {
            if (is_string($arrayName)) {
                if (!isset($parameters[$arrayName])) {
                    continue;
                }
                if (is_array($exclude)) {
                    foreach ($exclude as $excludeItem) {
                        if (isset($parameters[$arrayName][$excludeItem])) {
                            unset($parameters[$arrayName][$excludeItem]);
                        }
                    }
                } else {
                    if (isset($parameters[$arrayName][$exclude])) {
                        unset($parameters[$arrayName][$exclude]);
                    }
                }
                if (empty($parameters[$arrayName])) {
                    unset($parameters[$arrayName]);
                }
            } else {
                if (isset($parameters[$exclude])) {
                    unset($parameters[$exclude]);
                }
            }
        }
        $parameters = array_merge($parameters, $additionalParameters);

        return $parameters;
    }

    /**
     * @param array          $parameters
     * @param TdbCmsLanguage $language
     */
    private function modifyRouteParameters(array &$parameters, TdbCmsLanguage $language = null)
    {
        /*
         * _locale is set automatically to the new value and would be appended to the resulting link
         * if we didn't remove it here.
         */
        unset($parameters['_locale']);

        /*
         * When altering the language, language-specific route parameters will change, so we need to apply the parameters
         * for the new language. Currently we do this only for the pagePath as this is the only known parameter needing
         * this treatment.
         * This is quite a weird hack. Not only do we handle a special case in a very generic functionality, but also
         * we have no way to handle similar cases accordingly without adding them to the hack and making it even worse.
         * So this is subject to change if someone finds a better way (expectations are not that high after having
         * written THAT code).
         */
        if (null !== $language && $this->languageService->getActiveLanguageId() !== $language->id && isset($parameters['pagePath'])) {
            $activePage = $this->getActivePage();
            $activePortal = $activePage->GetPortal();
            if ($activePage->fieldPrimaryTreeIdHidden === $activePortal->fieldHomeNodeId) {
                unset($parameters['pagePath']);
            } else {
                $allPageRoutes = $this->routingUtil->getAllPageRoutes($activePortal, $language);
                $parameters['pagePath'] = rtrim($allPageRoutes[$activePage->id]->getPrimaryPath(), '/');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getLinkToActivePageAbsolute(array $additionalParameters = array(), array $excludeParameters = array(), TdbCmsLanguage $language = null, $forceSecure = false)
    {
        $url = $this->getLinkToActivePage($additionalParameters, $excludeParameters, $language, UrlGeneratorInterface::ABSOLUTE_URL);

        if (true === $forceSecure) {
            $url = $this->getSecureUrlIfNeeded($url);
        }

        return $url;
    }

    /**
     * Symfony currently does not allow to enforce generation of secure URLs (a secure URL will only be generated if the
     * route requires HTTPS or if the current request is secure), therefore we turn the URL secure manually.
     *
     * @param string              $url
     * @param TdbCmsLanguage|null $language
     *
     * @return string
     */
    private function getSecureUrlIfNeeded($url, TdbCmsLanguage $language = null)
    {
        if (false === $this->urlUtil->isUrlSecure($url)) {
            $url = $this->urlUtil->getAbsoluteUrl($url, true, null, null, $language);
        }

        return $url;
    }
}
