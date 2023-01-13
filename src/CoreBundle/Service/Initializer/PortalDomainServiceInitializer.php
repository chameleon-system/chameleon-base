<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Service\Initializer;

use ChameleonSystem\CoreBundle\DataAccess\CmsPortalDomainsDataAccessInterface;
use ChameleonSystem\CoreBundle\Exception\InvalidPortalDomainException;
use ChameleonSystem\CoreBundle\RequestType\RequestTypeInterface;
use ChameleonSystem\CoreBundle\Service\ActivePageServiceInterface;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\SecurityBundle\CmsUser\UserRoles;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use esono\pkgCmsCache\CacheInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class PortalDomainServiceInitializer.
 */
class PortalDomainServiceInitializer implements PortalDomainServiceInitializerInterface
{
    /**
     * @var InputFilterUtilInterface
     */
    private $inputFilterUtil;
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var CmsPortalDomainsDataAccessInterface
     */
    private $cmsPortalDomainsDataAccess;

    public function __construct(InputFilterUtilInterface $inputFilterUtil, ContainerInterface $container, RequestStack $requestStack, CmsPortalDomainsDataAccessInterface $cmsPortalDomainsDataAccess)
    {
        $this->inputFilterUtil = $inputFilterUtil;
        $this->container = $container; // avoid circular dependencies
        $this->requestStack = $requestStack;
        $this->cmsPortalDomainsDataAccess = $cmsPortalDomainsDataAccess;
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(PortalDomainServiceInterface $portalDomainService)
    {
        if (null === $request = $this->requestStack->getCurrentRequest()) {
            return;
        }

        $requestInfoService = $this->getRequestInfoService();
        if ($requestInfoService->isChameleonRequestType(RequestTypeInterface::REQUEST_TYPE_FRONTEND)
            && true === $requestInfoService->isCmsTemplateEngineEditMode()
        ) {
            list($portal, $domain) = $this->determinePortalAndDomainForCmsTemplateEngineMode($portalDomainService);
        } elseif ($requestInfoService->isChameleonRequestType(RequestTypeInterface::REQUEST_TYPE_BACKEND)) {
            $portal = null;
            $domain = null;
        } else {
            list($portal, $domain) = $this->determinePortalAndDomainDefault($request, $portalDomainService);
        }

        $portalDomainService->setActivePortal($portal);
        $portalDomainService->setActiveDomain($domain);
    }

    /**
     * @return array
     *
     * @throws InvalidPortalDomainException
     */
    private function determinePortalAndDomainForCmsTemplateEngineMode(PortalDomainServiceInterface $portalDomainService): array
    {
        $previewLanguageID = $this->inputFilterUtil->getFilteredInput('previewLanguageId');

        $portal = $this->getActivePageService()->getActivePage()->GetPortal();
        $domain = $portalDomainService->getPrimaryDomain($portal->id, $previewLanguageID);

        return array($portal, $domain);
    }

    /**
     * @param Request                      $request
     * @param PortalDomainServiceInterface $portalDomainService
     *
     * @return array
     *
     * @throws InvalidPortalDomainException
     */
    private function determinePortalAndDomainDefault(Request $request, PortalDomainServiceInterface $portalDomainService): array
    {
        $portal = null;
        $domain = null;

        $sName = $request->getHost();
        $sRelativePath = $request->getPathInfo();
        $isUserSignedInToBackend = false;
        if ($request->hasSession()) {
            /** @var SecurityHelperAccess $securityHelper */
            $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
            $isUserSignedInToBackend = $securityHelper->isGranted(UserRoles::CMS_USER);
        }

        $frontController = PATH_CUSTOMER_FRAMEWORK_CONTROLLER;
        if ('/' !== substr($frontController, 0, 1)) {
            $frontController = '/'.$frontController;
        }
        if ($frontController === $sRelativePath) {
            $pagedef = $request->attributes->get('pagedef');
            if (null !== $pagedef) {
                $oPage = \TdbCmsTplPage::GetNewInstance();
                if ($oPage->Load($pagedef)) {
                    $portal = $oPage->GetPortal();

                    if (true === $portal->fieldDeactivePortal && false === $isUserSignedInToBackend) {
                        $portal = null;
                    } else {
                        $previewLanguageId = $this->inputFilterUtil->getFilteredInput('previewLanguageId');
                        $domain = $portalDomainService->getPrimaryDomain($portal->id, $previewLanguageId);

                        return array($portal, $domain);
                    }
                }
            }
        }

        $prefix = '';
        if (\strlen($sRelativePath) > 1) {
            $secondSlashPosition = strpos($sRelativePath, '/', 1);
            if (false === $secondSlashPosition) {
                $prefix = substr($sRelativePath, 1);
            } else {
                $prefix = substr($sRelativePath, 1, $secondSlashPosition - 1);
            }
            if ('' !== $prefix) {
                $aPermittedPrefixList = $this->cmsPortalDomainsDataAccess->getPortalPrefixListForDomain($sName);
                if (false === in_array($prefix, $aPermittedPrefixList)) {
                    $prefix = '';
                }
            }
        }

        $aKey = array(
            'class' => __CLASS__,
            'method' => 'setPortalAndDomainFromRequest',
            'host' => $sName,
            'prefix' => $prefix,
            'userIsSignedIntoCMSBackend' => $isUserSignedInToBackend,
            'bTemplateEngineEditMode' => false,
        );

        $cache = $this->getCache();
        $sKey = $cache->getKey($aKey, false);

        $aResultData = $cache->get($sKey);
        if (null !== $aResultData) {
            $portal = $aResultData['portal'];
            $domain = $aResultData['domain'];

            return array($portal, $domain);
        }

        $aResultData = array(
            'portal' => null,
            'domain' => null,
        );

        $domainList = $this->cmsPortalDomainsDataAccess->getDomainDataByName($sName);

        $iPortalId = null;
        $domainCount = \count($domainList);
        // If we have more than one possible domain, we need to use the first part of the path as the portal prefix.
        if ($domainCount > 1) {
            $portalIdList = [];
            foreach ($domainList as $domain) {
                $portalIdList[] = $domain['cms_portal_id'];
            }

            $aPortal = $this->cmsPortalDomainsDataAccess->getActivePortalCandidate($portalIdList, $prefix, $isUserSignedInToBackend);
            if (null !== $aPortal) {
                $oPortal = \TdbCmsPortal::GetNewInstance($aPortal);
                $aResultData['portal'] = $oPortal;
                $iPortalId = $aPortal['id'];

                foreach ($domainList as $domain) {
                    if ($domain['cms_portal_id'] === $iPortalId) {
                        $aResultData['domain'] = \TdbCmsPortalDomains::GetNewInstance($domain);
                        break;
                    }
                }
            }
        } elseif (1 === $domainCount) {
            $aResultData['domain'] = \TdbCmsPortalDomains::GetNewInstance($domainList[0]);
        }

        if (null === $aResultData['portal']) {
            if (null === $iPortalId && $domainCount > 0) {
                $iPortalId = $domainList[0]['cms_portal_id'];
            }
            $portal = \TdbCmsPortal::GetNewInstance();
            if (false !== $portal->Load($iPortalId)) {
                if (false === $portal->fieldDeactivePortal || true === $isUserSignedInToBackend) {
                    $aResultData['portal'] = $portal;
                }
            }
        }

        $cache->set(
            $sKey,
            $aResultData,
            array(
                array('table' => 'cms_portal', 'id' => null),
                array('table' => 'cms_portal_domains', 'id' => null),
            )
        );

        $portal = $aResultData['portal'];
        $domain = $aResultData['domain'];

        return array($portal, $domain);
    }

    private function getRequestInfoService(): RequestInfoServiceInterface
    {
        return $this->container->get('chameleon_system_core.request_info_service');
    }

    private function getCache(): CacheInterface
    {
        return $this->container->get('chameleon_system_cms_cache.cache');
    }

    private function getActivePageService(): ActivePageServiceInterface
    {
        return $this->container->get('chameleon_system_core.active_page_service');
    }
}
