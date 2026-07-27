<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use ChameleonSystem\CoreBundle\Util\UrlPrefixGeneratorInterface;

/**
 * @deprecated use \ChameleonSystem\CoreBundle\ServiceLocator::get('request_stack')->getCurrentRequest() instead
 *
 * Lots of magic properties through `__get` in this class:
 *
 * @property string $sRelativeURL
 * @property string $sOriginalURL
 * @property TdbCmsPortalDomains|null $oActiveDomain
 * @property string $sDomainName
 * @property string $sOriginalDomainName
 * @property bool $bIsSSLCall
 * @property string $sRelativeURLPortalIdentifier
 * @property string $sRelativeFullURL
 * @property array $aParameters
 * @property string $iPortalId
 * @property bool $bPagedefFound
 * @property bool $bDomainNotFound
 * @property bool $bPortalNotFound
 * @property string $sLanguageId
 * @property string $sLanguageIdentifier
 * @property bool $bDomainBasedLanguage
 */
class TCMSSmartURLData
{
    /**
     * set to true when the object is completely loaded (seo handler processed etc.) after this, all parameters of the object have been set to the data from the request.
     *
     * @var bool
     */
    private $bObjectInitializationCompleted = false;

    /**
     * all parameters generated via seo handler from the original url.
     *
     * @var array
     */
    private $seoURLParameters;

    public function __isset($var)
    {
        $availableProperties = ['sRelativeURL',
            'sOriginalURL',
            'oActiveDomain',
            'sDomainName',
            'sOriginalDomainName',
            'bIsSSLCall',
            'sRelativeURLPortalIdentifier',
            'sRelativeFullURL',
            'aParameters',
            'iPortalId',
            'bPagedefFound',
            'bDomainNotFound',
            'bPortalNotFound',
            'sLanguageId',
            'sLanguageIdentifier',
            'bDomainBasedLanguage', ];

        if (in_array($var, $availableProperties)) {
            return true;
        } else {
            return false;
        }
    }

    public function __get($var)
    {
        $request = ChameleonSystem\CoreBundle\ServiceLocator::get('request_stack')->getCurrentRequest();
        $activePortal = $this->getPortalDomainService()->getActivePortal();
        $activeDomain = $this->getPortalDomainService()->getActiveDomain();
        $activeLanguage = $this->getLanguageService()->getActiveLanguage();

        switch ($var) {
            case 'sRelativeURL':
                return $this->getRequestInfoService()->getPathInfoWithoutPortalAndLanguagePrefix();
            case 'sOriginalURL':
                return $request->getPathInfo();
            case 'oActiveDomain':
                return $activeDomain;
            case 'sDomainName':
                return (null !== $activeDomain) ? $activeDomain->GetActiveDomainName() : null;
            case 'sOriginalDomainName':
                return $request->getHost();
            case 'bIsSSLCall':
                if (null === $request) {
                    return false;
                }

                return $request->isSecure();
            case 'sRelativeURLPortalIdentifier':
                return (null !== $activePortal) ? $activePortal->fieldIdentifier : null;
            case 'sRelativeFullURL':
                return $request->getPathInfo();
            case 'aParameters':
                return $request->query->all();
            case 'iPortalId':
                return ($activePortal) ? $activePortal->id : null;
            case 'bPagedefFound':
                return true;
            case 'bDomainNotFound':
                return null === $activeDomain;
            case 'bPortalNotFound':
                return null === $activePortal;
            case 'sLanguageId':
                return $this->getLanguageService()->getActiveLanguageId();
            case 'sLanguageIdentifier':
                return $this->getUrlPrefixGenerator()->getLanguagePrefix($activePortal, $activeLanguage);
            case 'bDomainBasedLanguage':
                return $activeDomain && '' !== $activeDomain->fieldCmsLanguageId;
        }

        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): '.$var.
            ' in '.$trace[0]['file'].
            ' on line '.$trace[0]['line'],
            E_USER_NOTICE);

        return null;
    }

    /**
     * get active instance of data object.
     *
     * @return TCMSSmartURLData
     *
     * @deprecated - you should use the request object instead:  $request = \ChameleonSystem\CoreBundle\ServiceLocator::get('request_stack')->getCurrentRequest(); // @var Request $request
     */
    public static function GetActive()
    {
        static $oInstance;
        if (!$oInstance) {
            // try to get from cache
            $oInstance = new self();
            $oInstance->Init();
        }

        return $oInstance;
    }

    /**
     * returns an array of cache parameters to identify the object in cache.
     *
     * @return array
     */
    public function GetCacheKeyParameters()
    {
        $aData = ['class' => __CLASS__, 'sRelativeURL' => $this->sRelativeURL, 'sDomainName' => $this->sDomainName, 'bIsSSLCall' => $this->bIsSSLCall, 'iPortalId' => $this->iPortalId, 'sOriginalDomainName' => $this->sOriginalDomainName, 'sLanguageId' => $this->sLanguageId];

        return $aData;
    }

    /**
     * returns multidimensional array of tables and ids to use as cache delete triggers.
     *
     * @return array
     */
    public function GetCacheTableInfos()
    {
        $aData = [];

        $oPortal = $this->GetPortal();
        /** @var $oPortal TdbCmsPortal */
        if (!is_null($oPortal)) {
            $aData[] = ['table' => 'cms_portal', 'id' => $oPortal->id];
        }
        $aData[] = ['table' => 'cms_portal_domains', 'id' => ''];
        $aData[] = ['table' => 'cms_tree_node', 'id' => ''];
        $aData[] = ['table' => 'cms_portal_navigation', 'id' => ''];

        return $aData;
    }

    public function Init()
    {
    }

    /**
     * get Portal or load Portal with portal id.
     *
     * return TdbCmsPortal|null
     *
     * @deprecated use chameleon_system_core.portal_domain_service::getActivePortal() instead
     */
    public function GetPortal()
    {
        return $this->getPortalDomainService()->getActivePortal();
    }

    /**
     * return the user ip... (takes proxy forwarding into consideration).
     *
     * @return string
     *
     * @deprecated since 6.2.0 - use Request::getClientIp() instead.
     */
    public static function GetUserIp()
    {
        $clientIp = '';
        $request = ChameleonSystem\CoreBundle\ServiceLocator::get('request_stack')->getCurrentRequest();
        if (null !== $request) {
            $clientIp = $request->getClientIp();
        }

        return $clientIp;
    }

    /**
     * set to true if all request processing has completed (ie. page is set, portal ist defined, language is defined, etc).
     *
     * @param bool $bObjectInitializationCompleted
     */
    public function SetObjectInitializationCompleted($bObjectInitializationCompleted)
    {
        $this->bObjectInitializationCompleted = $bObjectInitializationCompleted;
    }

    /**
     * return true if all request processing has completed (ie. page is set, portal ist defined, language is defined, etc).
     *
     * @return bool
     */
    public function IsObjectInitializationCompleted()
    {
        return $this->bObjectInitializationCompleted;
    }

    /**
     * @return array
     */
    public function getSeoURLParameters()
    {
        return $this->seoURLParameters;
    }

    /**
     * @param array $seoURLParameters
     */
    public function setSeoURLParameters($seoURLParameters)
    {
        $this->seoURLParameters = $seoURLParameters;
    }

    /**
     * @return RequestInfoServiceInterface
     */
    private function getRequestInfoService()
    {
        $requestInfoService = ChameleonSystem\CoreBundle\ServiceLocator::get(
            'chameleon_system_core.request_info_service'
        );

        return $requestInfoService;
    }

    /**
     * @return LanguageServiceInterface
     */
    private function getLanguageService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.language_service');
    }

    /**
     * @return PortalDomainServiceInterface
     */
    private function getPortalDomainService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.portal_domain_service');
    }

    /**
     * @return UrlPrefixGeneratorInterface
     */
    private function getUrlPrefixGenerator()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.util.url_prefix_generator');
    }
}
