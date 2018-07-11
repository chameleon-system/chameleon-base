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

use ChameleonSystem\CoreBundle\RequestType\RequestTypeInterface;
use ChameleonSystem\CoreBundle\Service\ActivePageServiceInterface;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ErrorException;
use esono\pkgCmsCache\CacheInterface;
use MySqlLegacySupport;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use TdbCmsPortal;
use TdbCmsPortalDomains;
use TdbCmsPortalDomainsList;
use TdbCmsTplPage;
use TPkgCmsException_Log;

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
     * @param InputFilterUtilInterface $inputFilterUtil
     * @param ContainerInterface       $container
     * @param RequestStack             $requestStack
     */
    public function __construct(InputFilterUtilInterface $inputFilterUtil, ContainerInterface $container, RequestStack $requestStack)
    {
        $this->inputFilterUtil = $inputFilterUtil;
        $this->container = $container; // avoid circular dependencies
        $this->requestStack = $requestStack;
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
     */
    private function determinePortalAndDomainForCmsTemplateEngineMode(PortalDomainServiceInterface $portalDomainService)
    {
        $previewLanguageID = $this->inputFilterUtil->getFilteredInput('previewLanguageId');

        $portal = $this->getActivePageService()->getActivePage()->GetPortal();
        $domain = $portalDomainService->getPrimaryDomain($portal->id, $previewLanguageID);

        return array($portal, $domain);
    }

    /**
     * @param Request $request
     *
     * @return array
     *
     * @throws ErrorException
     * @throws TPkgCmsException_Log
     */
    private function determinePortalAndDomainDefault(Request $request, PortalDomainServiceInterface $portalDomainService)
    {
        $portal = null;
        $domain = null;

        $sName = $request->getHost();
        $sRelativePath = $request->getPathInfo();
        $isUserSignedInToBackend = \TCMSUser::CMSUserDefined();

        $frontController = PATH_CUSTOMER_FRAMEWORK_CONTROLLER;
        if ('/' !== substr($frontController, 0, 1)) {
            $frontController = '/'.$frontController;
        }
        if ($frontController === $sRelativePath) { // index.php
            $pagedef = $request->query->get('pagedef', null);
            if (null !== $pagedef) {
                $oPage = TdbCmsTplPage::GetNewInstance();
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
        if (strlen($sRelativePath) > 1) {
            $secondSlashPosition = strpos($sRelativePath, '/', 1);
            if (false === $secondSlashPosition) {
                $prefix = substr($sRelativePath, 1);
            } else {
                $prefix = substr($sRelativePath, 1, $secondSlashPosition - 1);
            }
            if ('' !== $prefix) {
                $aPermittedPrefixList = $this->getPortalPrefixList($sName);
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
            'portalIdentifier' => null,
        );

        $query = "SELECT * FROM `cms_portal_domains`
                 WHERE (`name` = '{$sName}' AND `name` != '')
                    OR (`sslname` = '{$sName}' AND `sslname` != '')
              GROUP BY `cms_portal_domains`.`cms_portal_id`
               ";

        $oPortalDomainsList = TdbCmsPortalDomainsList::GetList($query);
        $iPortalId = null;
        // if we have more than one possible domain, we need to use the first part of the path as the portal
        // prefix.
        if ($oPortalDomainsList->Length() > 1) {
            // this works only if we have a part in the path
            $tmpPath = $sRelativePath;
            if ('/' !== substr($tmpPath, 0, 1)) {
                $tmpPath = '/'.$tmpPath;
            }
            $aPathParts = explode('/', $tmpPath);
            if ('/' !== $tmpPath && is_array($aPathParts) && count($aPathParts) > 1) {
                $tmpPath = $aPathParts[1];
            } elseif (is_array($aPathParts)) {
                $tmpPath = $aPathParts[0];
            } else {
                $tmpPath = '';
            }

            $sIDs = '';
            while ($oPortalDomain = $oPortalDomainsList->Next()) {
                $sIDs .= "'".MySqlLegacySupport::getInstance()->real_escape_string(
                        $oPortalDomain->sqlData['cms_portal_id']
                    )."',";
            }
            $sIDs = substr($sIDs, 0, -1); // cut comma
            $oPortalDomainsList->GoToStart();

            // allow access to deactivated portals for backend users
            $sRestrictToActivePortals = "AND `cms_portal`.`deactive_portal` != '1'";
            if (true === $isUserSignedInToBackend) {
                $sRestrictToActivePortals = '';
            }
            $query = 'SELECT *
                    FROM `cms_portal`
                   WHERE `id` IN ('.$sIDs.")
                     AND (`identifier` = '".MySqlLegacySupport::getInstance()->real_escape_string($tmpPath)."' OR `identifier` = '')
                     {$sRestrictToActivePortals}
                ORDER BY `identifier` DESC
                   LIMIT 0,1
                 ";
            $aPortal = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query));
            if (false != $aPortal) {
                $oPortal = TdbCmsPortal::GetNewInstance();
                $oPortal->LoadFromRow($aPortal);
                $aResultData['portal'] = $oPortal;
                $iPortalId = $aPortal['id'];
                // drop the portal identifier from the path
                $aPortal['identifier'] = trim($aPortal['identifier']);
                if (!empty($aPortal['identifier'])) {
                    $aResultData['portalIdentifier'] = '/'.$aPortal['identifier'];
                } else {
                    $aResultData['portalIdentifier'] = '';
                }

                while ($oPortalDomain = $oPortalDomainsList->Next()) {
                    if ($oPortalDomain->sqlData['cms_portal_id'] == $iPortalId) {
                        $aResultData['domain'] = $oPortalDomain;
                        break;
                    }
                }
            }
        } elseif (1 == $oPortalDomainsList->Length()) {
            // if we have only one portal, but the portal has a prefix, we need to remove it from the path
            /** @var $oPortalDomain TdbCmsPortalDomains */
            $oPortalDomain = $oPortalDomainsList->Current();
            $aResultData['domain'] = $oPortalDomain;
            $query = "SELECT `identifier` FROM `cms_portal` WHERE `id` = '".MySqlLegacySupport::getInstance(
                )->real_escape_string($oPortalDomain->sqlData['cms_portal_id'])."' ";
            if ($aTmpPortal = MySqlLegacySupport::getInstance()->fetch_row(
                MySqlLegacySupport::getInstance()->query($query)
            )
            ) {
                $sIdent = trim($aTmpPortal[0]);
                $aResultData['portalIdentifier'] = '';
                if (!empty($sIdent)) {
                    $sIdent = '/'.$sIdent;
                    $aResultData['portalIdentifier'] = $sIdent;
                }
            }
        }
        if (null === $iPortalId) {
            $oPortalDomain = $oPortalDomainsList->Current();
            if (is_object($oPortalDomain)) {
                $iPortalId = $oPortalDomain->sqlData['cms_portal_id'];
            }
        }

        if (null === $aResultData['portal']) {
            $aResultData['portal'] = TdbCmsPortal::GetNewInstance();
            if (false === $aResultData['portal']->Load($iPortalId)) {
                $aResultData['portal'] = null;
            } elseif (true === $aResultData['portal']->fieldDeactivePortal && false === $isUserSignedInToBackend) {
                $aResultData['portal'] = null;
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

    /**
     * return an array of prefix for the portal.
     *
     * @param string $domain
     *
     * @return array
     */
    private function getPortalPrefixList($domain)
    {
        $cache = $this->getCache();
        $key = $cache->getKey(array(
            'class' => __CLASS__,
            'method' => 'getPortalPrefixList',
            'domain' => $domain,
        ),
            false
        );
        $prefixList = $cache->get($key);
        if (null !== $prefixList) {
            return $prefixList;
        }

        $prefixList = array();
        $query = "SELECT cms_portal.identifier
                    FROM `cms_portal_domains`
              INNER JOIN `cms_portal` ON `cms_portal_domains`.`cms_portal_id` = `cms_portal`.`id`
                 WHERE (`cms_portal_domains`.`name` = '{$domain}' AND `cms_portal_domains`.`name` != '')
                    OR (`cms_portal_domains`.`sslname` = '{$domain}' AND `cms_portal_domains`.`sslname` != '')
              GROUP BY `cms_portal_domains`.`cms_portal_id`
               ";
        $tRes = MySqlLegacySupport::getInstance()->query($query);
        while ($row = MySqlLegacySupport::getInstance()->fetch_assoc($tRes)) {
            $prefixList[] = $row['identifier'];
        }

        $cache->set($key, $prefixList, array(array('table' => 'cms_portal', 'id' => null), array('table' => 'cms_portal_domains', 'id' => null)));

        return $prefixList;
    }

    /**
     * @return RequestInfoServiceInterface
     */
    private function getRequestInfoService()
    {
        return $this->container->get('chameleon_system_core.request_info_service');
    }

    /**
     * @return CacheInterface
     */
    private function getCache()
    {
        return $this->container->get('chameleon_system_cms_cache.cache');
    }

    /**
     * @return ActivePageServiceInterface
     */
    private function getActivePageService()
    {
        return $this->container->get('chameleon_system_core.active_page_service');
    }
}
