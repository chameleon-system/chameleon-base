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

use ChameleonSystem\CoreBundle\Exception\InvalidLanguageException;
use ChameleonSystem\CoreBundle\RequestType\RequestTypeInterface;
use ChameleonSystem\CoreBundle\Service\ActivePageServiceInterface;
use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use Doctrine\DBAL\Connection;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use TdbCmsConfig;
use TdbCmsPortal;

/**
 * Class LanguageServiceInitializer.
 */
class LanguageServiceInitializer implements LanguageServiceInitializerInterface
{
    /**
     * @var InputFilterUtilInterface
     */
    private $inputFilterUtil;
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var Container
     */
    private $container;
    /**
     * @var Connection
     */
    private $databaseConnection;

    /**
     * @param InputFilterUtilInterface $inputFilterUtil
     * @param RequestStack             $requestStack
     * @param Container                $container
     * @param Connection               $databaseConnection
     */
    public function __construct(InputFilterUtilInterface $inputFilterUtil, RequestStack $requestStack, Container $container, Connection $databaseConnection)
    {
        $this->inputFilterUtil = $inputFilterUtil;
        $this->requestStack = $requestStack;
        $this->container = $container;
        $this->databaseConnection = $databaseConnection;
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(LanguageServiceInterface $languageService)
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return;
        }
        $requestInfoService = $this->getRequestInfoService();
        if ($requestInfoService->isChameleonRequestType(RequestTypeInterface::REQUEST_TYPE_FRONTEND)
            && true === $requestInfoService->isCmsTemplateEngineEditMode()
        ) {
            $languageId = $this->determineLanguageForCmsTemplateEngineMode();
        } else {
            $languageId = $this->determineLanguageDefault();
        }

        $languageService->setActiveLanguage($languageId);
    }

    /**
     * @return string|null
     */
    private function determineLanguageForCmsTemplateEngineMode()
    {
        /** @var string|null $previewLanguageId */
        $previewLanguageId = $this->inputFilterUtil->getFilteredInput('previewLanguageId');

        if (null !== $previewLanguageId) {
            $languageId = $previewLanguageId;
        } else {
            $languageId = $this->determineLanguageDefault();
        }

        return $languageId;
    }

    /**
     * @return string|null
     */
    private function determineLanguageDefault()
    {
        $languageId = null;
        if ($this->getRequestInfoService()->isChameleonRequestType(RequestTypeInterface::REQUEST_TYPE_BACKEND)) {
            $oCmsUser = \TCMSUser::GetActiveUser();
            if ($oCmsUser && is_array($oCmsUser->sqlData) && isset($oCmsUser->sqlData['cms_language_id'])) {
                $languageId = $oCmsUser->sqlData['cms_language_id'];
            }
        } else {
            try {
                $languageId = $this->getLanguageFromRequestData($this->requestStack->getCurrentRequest());
            } catch (InvalidLanguageException $e) {
                $languageId = $this->getFallbackLanguage();
            }
        }

        return $languageId;
    }

    /**
     * + default
     * + portal
     * + division
     * + page
     * + url prefix
     * + domain.
     *
     * special rule: can be overwritten by previewLanguageId in __previewmode
     *
     * @param Request $request
     *
     * @return string|null
     *
     * @throws \ErrorException
     * @throws \TPkgCmsException_Log
     * @throws \Exception
     */
    protected function getLanguageFromRequestData(Request $request)
    {
        $sLanguageId = null;

        // special rule: can be overwritten by previewLanguageId in __previewmode
        /** @var string|null $previewMode */
        $previewMode = $this->inputFilterUtil->getFilteredInput('__previewmode', null);
        /** @var string|null $previewLanguageId */
        $previewLanguageId = $this->inputFilterUtil->getFilteredInput('previewLanguageId', null);
        if (null !== $previewMode && null !== $previewLanguageId) {
            return $previewLanguageId;
        }

        $portalDomainService = $this->getPortalDomainService();
        // language set via domain
        $domain = $portalDomainService->getActiveDomain();
        if (null === $domain) {
            return null;
        }

        if (!empty($domain->fieldCmsLanguageId)) {
            return $domain->fieldCmsLanguageId;
        }

        // language set via url prefix?
        $activePortal = $portalDomainService->getActivePortal();
        if (null === $activePortal) {
            return null;
        }

        if (true === $activePortal->fieldUseMultilanguage) {
            $languageId = $this->getLanguageFromUri($request, $activePortal);
            if (null !== $languageId) {
                return $languageId;
            }
        }

        $activePage = $this->getActivePageService()->getActivePage();
        if (null !== $activePage && !empty($activePage->fieldCmsLanguageId)) {
            return $activePage->fieldCmsLanguageId;
        }

        $sLanguageId = $activePortal->fieldCmsLanguageId;
        if ('' !== $sLanguageId) {
            return $sLanguageId;
        }

        $config = TdbCmsConfig::GetInstance();
        if (property_exists(
                $config,
                'fieldTranslationBaseLanguageId'
            ) && '' !== $config->fieldTranslationBaseLanguageId
        ) {
            return $config->fieldTranslationBaseLanguageId;
        }

        throw new \Exception('fallback language requested, but none defined. Please set your fallback language in the cms_config table (field translation_base_language)');
    }

    /**
     * @param Request      $request
     * @param TdbCmsPortal $activePortal
     *
     * @return string|null
     *
     * @throws InvalidLanguageException
     */
    private function getLanguageFromUri(Request $request, TdbCmsPortal $activePortal)
    {
        $sRelativePath = $request->getPathInfo();
        $sRelativePath = substr($sRelativePath, 1); // remove "/";
        $aPathParts = explode('/', $sRelativePath);
        $iLangIndex = 0;
        if ('' !== $activePortal->fieldIdentifier && count($aPathParts) > 0) {
            ++$iLangIndex;
        }
        $languageId = null;
        if (isset($aPathParts[$iLangIndex])) {
            $languageCode = $aPathParts[$iLangIndex];
            if (2 === strlen($languageCode)) {
                $languageId = $this->getLanguageFromPersistence($activePortal, $languageCode);
            }
        }

        return $languageId;
    }

    /**
     * @param TdbCmsPortal $activePortal
     * @param string       $languageCode
     *
     * @return string|null
     *
     * @throws InvalidLanguageException if the language was found, but is not available in the frontend in the $activePortal
     */
    public function getLanguageFromPersistence(TdbCmsPortal $activePortal, $languageCode)
    {
        $query = $this->getLanguageQuery();
        $statement = $this->databaseConnection->prepare($query);
        $statement->execute(array(
            'languageCode' => $languageCode,
        ));
        $theLanguage = null;
        $languageFound = false;
        while ($row = $statement->fetch()) {
            $languageFound = true;
            $portalId = $row['portal_id'];
            $languageId = $row['language_id'];
            $isActiveForFrontend = $row['active_for_front_end'];

            if (($portalId === $activePortal->id)
                && (('1' === $isActiveForFrontend) || $activePortal->GetActivateAllPortalLanguages())) {
                $theLanguage = $languageId;
                break;
            }
        }

        if ($languageFound && null === $theLanguage) {
            throw new InvalidLanguageException('Language was requested, but this language is not available for the active portal: '.$languageCode);
        }

        // if the language was not found, we assume that the $languageCode wasn't a real language code but some other 2-letter token
        return $theLanguage;
    }

    /**
     * @return string
     */
    private function getLanguageQuery()
    {
        return 'SELECT pl.`source_id` as portal_id, l.`id` as language_id, l.`active_for_front_end`
                  FROM `cms_portal_cms_language_mlt` AS pl
                  RIGHT OUTER JOIN `cms_language` AS l
                  ON pl.`target_id` = l.`id`
                  WHERE l.`iso_6391` = :languageCode';
    }

    /**
     * the fallback language is either the one set by the portal, or the one set in cms_config.translation_base_language_id.
     *
     * @return string
     *
     * @throws \Exception if no fallback language is found
     */
    private function getFallbackLanguage()
    {
        $activePortal = $this->getPortalDomainService()->getActivePortal();
        if (null !== $activePortal) {
            $sLanguageId = $activePortal->fieldCmsLanguageId;
            if ('' !== $sLanguageId) {
                return $sLanguageId;
            }
        }

        $activePage = $this->getActivePageService()->getActivePage();
        if (null !== $activePage) {
            $pageLanguage = $activePage->GetLanguageID();

            if (null !== $pageLanguage) {
                return $pageLanguage;
            }
        }

        $config = TdbCmsConfig::GetInstance();
        if (property_exists($config, 'fieldTranslationBaseLanguageId') && '' !== $config->fieldTranslationBaseLanguageId) {
            return $config->fieldTranslationBaseLanguageId;
        }

        throw new \Exception('Fallback language requested, but none defined. Please set your fallback language in the cms_config table (field translation_base_language)');
    }

    /**
     * @param LanguageServiceInterface $languageService
     */
    public function initializeFallbackLanguage(LanguageServiceInterface $languageService)
    {
        if (!class_exists('\TdbCmsLanguage')) {
            return;
        }
        $fallbackLanguageId = $this->getFallbackLanguage();
        $languageService->setFallbackLanguage($languageService->getLanguage($fallbackLanguageId, $fallbackLanguageId));
    }

    /**
     * @return PortalDomainServiceInterface
     */
    private function getPortalDomainService()
    {
        return $this->container->get('chameleon_system_core.portal_domain_service');
    }

    /**
     * @return RequestInfoServiceInterface
     */
    private function getRequestInfoService()
    {
        return $this->container->get('chameleon_system_core.request_info_service');
    }

    /**
     * @return ActivePageServiceInterface
     */
    private function getActivePageService()
    {
        return $this->container->get('chameleon_system_core.active_page_service');
    }
}
