<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Routing;

use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use ChameleonSystem\CoreBundle\Util\RoutingUtilInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ChameleonFrontendRouter extends ChameleonBaseRouter implements PortalAndLanguageAwareRouterInterface
{
    /**
     * @var LanguageServiceInterface
     */
    private $languageService;
    /**
     * @var PortalDomainServiceInterface
     */
    private $portalDomainService;
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var RoutingUtilInterface
     */
    private $routingUtil;
    /**
     * @var string
     */
    private $controllerId;
    /**
     * @var RequestInfoServiceInterface
     */
    private $requestInfoService;
    /**
     * @var DomainValidatorInterface
     */
    private $domainValidator;

    /**
     * {@inheritdoc}
     */
    public function matchRequest(Request $request): array
    {
        $pagedefParamRoute = $this->getPagedefParamRoute($request);
        if (null !== $pagedefParamRoute) {
            return $pagedefParamRoute;
        }

        return parent::matchRequest($request);
    }

    /**
     * Returns a match if there is no specific path, but a pagedef argument (GET or POST).
     *
     * @return array|null
     */
    private function getPagedefParamRoute(Request $request)
    {
        $pagedef = $request->get('pagedef');
        if (false === $this->isPagedefParamRoute($request, $pagedef)) {
            return null;
        }

        $match = [];
        $match['pagedef'] = $pagedef;
        $match['_route'] = 'cms_pagedef';
        $match['_controller'] = $this->controllerId;
        $match['pagePath'] = PATH_CUSTOMER_FRAMEWORK_CONTROLLER;

        return $match;
    }

    /**
     * @param string $pagedef
     *
     * @return bool
     */
    private function isPagedefParamRoute(Request $request, $pagedef)
    {
        if (null === $pagedef) {
            return false;
        }
        if (PATH_CMS_CONTROLLER_FRONTEND !== $request->getPathInfo()) {
            return false;
        }
        if (false === $this->requestInfoService->isCmsTemplateEngineEditMode() && true === $this->requestInfoService->isPreviewMode()) {
            return false;
        }

        return true;
    }

    protected function generateCacheDirPath(string $baseCacheDir): string
    {
        return sprintf('%s/frontend', $baseCacheDir);
    }

    /**
     * {@inheritdoc}
     */
    protected function getRouterConfig()
    {
        $configArray = [];
        $query = <<<SQL
SELECT `pkg_cms_routing`.*, `cms_portal`.`id` AS portal_id
FROM `pkg_cms_routing`
LEFT JOIN `pkg_cms_routing_cms_portal_mlt` ON `pkg_cms_routing`.`id` = `pkg_cms_routing_cms_portal_mlt`.`source_id`
JOIN `cms_portal`
WHERE (`pkg_cms_routing_cms_portal_mlt`.`target_id` IS NULL OR `pkg_cms_routing_cms_portal_mlt`.`target_id` = `cms_portal`.`id`)
  AND `pkg_cms_routing`.`active` = '1'
ORDER BY `cms_portal`.`identifier` DESC, `pkg_cms_routing`.`position` ASC
SQL;
        $configList = \TdbPkgCmsRoutingList::GetList($query);
        while ($config = $configList->Next()) {
            $configArray[] = $config->sqlData;
        }

        /*
         * Ensure that the portal without identifier is last so that the generated routes for that portal do not match
         * all requests before the other portals had a chance (the portal identfier would be considered part of the
         * route).
         */
        $portalList = \TdbCmsPortalList::GetList('SELECT `cms_portal`.* FROM `cms_portal` ORDER BY `identifier` DESC');
        while ($portal = $portalList->Next()) {
            $configArray = array_merge($configArray, $this->getAdditionalRoutingResources($portal));
        }

        return $configArray;
    }

    /**
     * @return array
     */
    protected function getAdditionalRoutingResources(\TdbCmsPortal $portal)
    {
        $routerConfig[] = [
            'name' => 'cms_image_not_found',
            'resource' => '@ChameleonSystemCoreBundle/Resources/config/route_image_not_found.yml',
            'type' => 'yaml',
            'portal_id' => $portal->id,
        ];

        $routerConfig[] = [
            'name' => 'cms_tpl_page',
            'resource' => '@ChameleonSystemCoreBundle/Resources/config/route_final.yml',
            'type' => 'yaml',
            'portal_id' => $portal->id,
        ];

        return $routerConfig;
    }

    /**
     * {@inheritdoc}
     *
     * Additional notes to the domain parameter (also see the description in the PortalAndLanguageAwareRouterInterface):
     * If this parameter is NOT passed, the domain is determined by the following logic: If the constant
     * CHAMELEON_FORCE_PRIMARY_DOMAIN is set to true, the primary domain configured in the backend will be used. Else
     * the currently active domain will be used.
     * If the domain parameter is passed, it will be used as long as it is a valid domain for the given portal
     * (if invalid, the primary domain will be used). Note that $referenceType will be changed to
     * UrlGeneratorInterface::ABSOLUTE_URL if the domain differs from the current domain and $referenceType is
     * RELATIVE_PATH or ABSOLUTE_PATH.
     */
    public function generateWithPrefixes($name, array $parameters = [], ?\TdbCmsPortal $portal = null, ?\TdbCmsLanguage $language = null, $referenceType = self::ABSOLUTE_PATH)
    {
        if (null === $portal) {
            $portal = $this->portalDomainService->getActivePortal();
        }
        if (null === $portal) {
            throw new RouteNotFoundException('Portal expected, but was not passed and could not be retrieved from PortalDomainService. Route name: '.$name);
        }
        if (null === $language) {
            $language = $this->languageService->getActiveLanguage();
        }
        if (null === $language) {
            $language = $this->languageService->getCmsBaseLanguage();
        }
        $name = $this->getFinalRouteName($name, $portal, $language);
        $domainParamName = $this->routingUtil->getHostRequirementPlaceholder();

        if (isset($parameters[$domainParamName])) {
            $url = $this->getUrlForCustomDomain($name, $portal, $language, $parameters, $domainParamName, $referenceType);
        } else {
            $url = $this->getUrlForDefaultDomain($name, $portal, $language, $parameters, $domainParamName, $referenceType);
        }

        return $url;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    private function getFinalRouteName($name, \TdbCmsPortal $portal, \TdbCmsLanguage $language)
    {
        return $name.'-'.$portal->id.'-'.$language->fieldIso6391;
    }

    /**
     * @param string $routeName
     * @param string $domainParamName
     * @param int $referenceType
     *
     * @psalm-param UrlGeneratorInterface::* $referenceType
     *
     * @return string
     */
    private function getUrlForCustomDomain($routeName, \TdbCmsPortal $portal, \TdbCmsLanguage $language, array $parameters, $domainParamName, $referenceType)
    {
        $parameters[$domainParamName] = $this->domainValidator->getValidDomain($parameters[$domainParamName], $portal, $language, $this->isForceSecure());
        if ($this->isRelativeReferenceType($referenceType)) {
            $activeDomain = $this->portalDomainService->getActiveDomain();
            if (null === $activeDomain || $activeDomain->GetActiveDomainName() !== $parameters[$domainParamName]) {
                $referenceType = UrlGeneratorInterface::ABSOLUTE_URL;
            }
        }

        return $this->generate($routeName, $parameters, $referenceType);
    }

    /**
     * @param int $referenceType
     *
     * @psalm-param UrlGeneratorInterface::* $referenceType
     *
     * @return bool
     */
    private function isRelativeReferenceType($referenceType)
    {
        return UrlGeneratorInterface::ABSOLUTE_PATH === $referenceType || UrlGeneratorInterface::RELATIVE_PATH === $referenceType;
    }

    /**
     * @param string $routeName
     * @param string $domainParamName
     * @param int $referenceType
     *
     * @psalm-param UrlGeneratorInterface::* $referenceType
     *
     * @return string
     */
    private function getUrlForDefaultDomain($routeName, \TdbCmsPortal $portal, \TdbCmsLanguage $language, array $parameters, $domainParamName, $referenceType)
    {
        $secure = $this->isForceSecure();
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            $domain = null;
        } else {
            try {
                $domain = $request->getHost();
            } catch (\UnexpectedValueException $e) {
                $domain = $this->getDomainFromActiveDomain();
            }
        }
        $parameters[$domainParamName] = $this->domainValidator->getValidDomain($domain, $portal, $language, $secure);
        if ($this->isRelativeReferenceType($referenceType) && $domain !== $parameters[$domainParamName]) {
            $referenceType = UrlGeneratorInterface::ABSOLUTE_URL;
        }
        if ($secure) {
            return $this->generate($routeName, $parameters, $referenceType);
        }

        /*
         * If the target page needs to be accessed via HTTPS and the HTTPS domain differs from the HTTP domain,
         * URL generation can fail because the current (non-HTTPS) primary domain does not match the route's
         * domain requirement. In this case, we retry using the HTTPS domain (this is a workaround - if
         * possible we would ask the generator for the route information).
         */
        try {
            return $this->generate($routeName, $parameters, $referenceType);
        } catch (InvalidParameterException $e) {
            $parameters[$domainParamName] = $this->portalDomainService->getPrimaryDomain($portal->id, $language->id)->getSecureDomainName();

            return $this->generate($routeName, $parameters, $referenceType);
        }
    }

    /**
     * @return bool
     */
    private function isForceSecure()
    {
        $request = $this->requestStack->getCurrentRequest();

        return null === $request || $request->isSecure();
    }

    /**
     * @return string|null
     */
    private function getDomainFromActiveDomain()
    {
        $activeDomain = $this->portalDomainService->getActiveDomain();
        if (null === $activeDomain) {
            return null;
        }

        return $activeDomain->GetActiveDomainName();
    }

    /**
     * @return void
     */
    public function setLanguageService(LanguageServiceInterface $languageService)
    {
        $this->languageService = $languageService;
    }

    /**
     * @return void
     */
    public function setPortalDomainService(PortalDomainServiceInterface $portalDomainService)
    {
        $this->portalDomainService = $portalDomainService;
    }

    /**
     * @return void
     */
    public function setRequestStack(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @return void
     */
    public function setRoutingUtil(RoutingUtilInterface $routingUtil)
    {
        $this->routingUtil = $routingUtil;
    }

    /**
     * @param string $controllerId
     *
     * @return void
     */
    public function setControllerId($controllerId)
    {
        $this->controllerId = $controllerId;
    }

    /**
     * @return void
     */
    public function setRequestInfoService(RequestInfoServiceInterface $requestInfoService)
    {
        $this->requestInfoService = $requestInfoService;
    }

    /**
     * @return void
     */
    public function setDomainValidator(DomainValidatorInterface $domainValidator)
    {
        $this->domainValidator = $domainValidator;
    }
}
