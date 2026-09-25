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
use ChameleonSystem\CoreBundle\Service\PageServiceInterface;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use esono\pkgCmsCache\CacheInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class LanguageServiceInitializer implements LanguageServiceInitializerInterface
{
    public function __construct(private readonly InputFilterUtilInterface $inputFilterUtil,
        private readonly RequestStack $requestStack,
        private readonly Container $container,
        private readonly Connection $databaseConnection,
        private readonly LoggerInterface $logger,
    )
    {
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
     *
     * @throws \Exception
     */
    private function determineLanguageForCmsTemplateEngineMode()
    {
        /** @var string|null $previewLanguageId */
        $previewLanguageId = $this->getPreviewLanguageId();

        if (null !== $previewLanguageId) {
            $languageId = $previewLanguageId;
        } else {
            $languageId = $this->determineLanguageDefault();
        }

        return $languageId;
    }

    /**
     * @throws \Exception
     */
    private function determineLanguageDefault(): ?string
    {
        $languageId = null;
        if ($this->getRequestInfoService()->isChameleonRequestType(RequestTypeInterface::REQUEST_TYPE_BACKEND)) {
            /** @var SecurityHelperAccess $securityHelper */
            $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

            $languageId = $securityHelper->getUser()?->getCmsLanguageId();
        } else {
            try {
                $languageId = $this->getLanguageFromRequestData($this->requestStack->getCurrentRequest());
            } catch (InvalidLanguageException $e) {
                $this->logger->warning($e->getMessage());
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
        $previewMode = $this->isPreviewMode();
        /** @var string|null $previewLanguageId */
        $previewLanguageId = $this->getPreviewLanguageId();
        if (true === $previewMode && null !== $previewLanguageId) {
            return $previewLanguageId;
        }

        $portalDomainService = $this->getPortalDomainService();
        $domain = $portalDomainService->getActiveDomain();
        if (null === $domain) {
            return null;
        }

        // language set via url prefix?
        $activePortal = $portalDomainService->getActivePortal();
        if (null === $activePortal) {
            return null;
        }

        // language set via url prefix?
        if (true === $activePortal->fieldUseMultilanguage) {
            $languageId = $this->getLanguageFromUri($request, $activePortal, $domain);
            if (null !== $languageId) {
                return $languageId;
            }
        }

        if (false === empty($domain->fieldCmsLanguageId)) {
            return $domain->fieldCmsLanguageId;
        }

        $activePage = $this->getActivePageService()->getActivePage();
        if (null !== $activePage && !empty($activePage->fieldCmsLanguageId)) {
            return $activePage->fieldCmsLanguageId;
        }

        $sLanguageId = $activePortal->fieldCmsLanguageId;
        if ('' !== $sLanguageId) {
            return $sLanguageId;
        }

        return \TdbCmsConfig::GetInstance()?->fieldTranslationBaseLanguageId;
    }

    /**
     * @throws Exception
     * @throws InvalidLanguageException
     */
    private function getPermittedLanguageForPortalDomain(\TdbCmsPortal $activePortal, \TdbCmsPortalDomains $domain): array
    {
        $cache = $this->getCacheService();
        $key = $cache->getKey(
            [
                'method'=>__METHOD__,
                'id'=>$activePortal->id,
                'domain'=> $domain->id,
                'enable_all_languages_for_frontend' => $activePortal->GetActivateAllPortalLanguages()
            ], false);
        $permittedLanguages = $cache->get($key);
        if (null !== $permittedLanguages) {
            return $permittedLanguages;
        }
        $permittedIds = $domain->getDomainLanguageIds();
        if ([] === $permittedIds) {
            $permittedIds = $activePortal->GetFieldCmsLanguageIdList();
        }
        if ([] === $permittedIds) {
            return [];
        }

        $query = "SELECT LANG.`id`, 
                         LANG.`iso_6391`, 
                         LANG.`url_prefix`, 
                         COALESCE(NULLIF(LANG.`url_prefix`, ''), LANG.`iso_6391`) AS url_alias
                    FROM cms_language AS LANG
              INNER JOIN cms_portal_cms_language_mlt AS PORTAL_LANG ON PORTAL_LANG.target_id = LANG.id
                   WHERE PORTAL_LANG.source_id = :portalId AND LANG.id IN (:permittedIds)";
        if (false === $activePortal->GetActivateAllPortalLanguages()) {
            $query .= " AND LANG.`active_for_front_end` = '1'";
        }
        $permittedLanguageRows = $this->databaseConnection->fetchAllAssociative($query,
            [
                'portalId' => $activePortal->id,
                'permittedIds' => $permittedIds
            ],
            [
                'permittedIds' => Connection::PARAM_STR_ARRAY
            ]
        );
        $permittedLanguages = [];
        foreach ($permittedLanguageRows as $permittedLanguageRow) {
            if (true === isset($permittedLanguages[$permittedLanguageRow['url_alias']])) {
                throw new InvalidLanguageException(
                    sprintf(
                        "There is more than one language for the active portal (%s) and domain (%s) that is set to use the url same prefix '%s'. language %s and %s",
                        $activePortal->id, $domain->id,
                        $permittedLanguageRow['url_alias'],
                        $permittedLanguages[$permittedLanguageRow['url_alias']]['id'],
                        $permittedLanguageRow['id'],
                    )
                );
            }
            $permittedLanguages[$permittedLanguageRow['url_alias']] = $permittedLanguageRow;
        }
        $cache->set($key, $permittedLanguages, [
            ['table'=>'cms_portal', 'id'=>$activePortal->id],
            ['table'=>'cms_portal_domains', 'id'=>$domain->id],
            ['table'=>'cms_language', 'id'=>null]
        ]);

        return $permittedLanguages;
    }

    /**
     * @return string|null
     *
     * @throws InvalidLanguageException
     */
    private function getLanguageFromUri(Request $request, \TdbCmsPortal $activePortal, \TdbCmsPortalDomains $domain)
    {
        $permittedLanguages = $this->getPermittedLanguageForPortalDomain($activePortal, $domain);

        if ([] === $permittedLanguages) {
            return null;
        }

        $sRelativePath = $request->getPathInfo();
        $sRelativePath = substr($sRelativePath, 1); // remove "/";
        $aPathParts = explode('/', $sRelativePath);
        $iLangIndex = 0;
        if ('' !== $activePortal->fieldIdentifier && count($aPathParts) > 0) {
            ++$iLangIndex;
        }
        if (false === isset($aPathParts[$iLangIndex])) {
            return null;
        }

        $languageCode = $aPathParts[$iLangIndex];
        if (empty($languageCode)) {
            return null;
        }

        if (false === isset($permittedLanguages[$languageCode])) {
            return null;
        }

        return $permittedLanguages[$languageCode]['id'];
    }

    /**
     * @param string $languageCode
     *
     * @return string|null
     *
     * @throws InvalidLanguageException if the language was found, but is not available in the frontend in the $activePortal
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function getLanguageFromPersistence(\TdbCmsPortal $activePortal, $languageCode)
    {
        $query = $this->getLanguageQuery();
        $rows = $this->databaseConnection->fetchAllAssociative($query, [
            'languageCode' => $languageCode,
        ]);
        $theLanguage = null;
        $languageFound = false;
        foreach ($rows as $row) {
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

    private function getLanguageQuery(): string
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
     * @throws \Exception if no fallback language is found
     */
    private function getFallbackLanguage(): string
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

        $config = \TdbCmsConfig::GetInstance();

        if (null === $config) {
            return '';
        }

        return $config->fieldTranslationBaseLanguageId;
    }

    /**
     * {@inheritDoc}
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
     * @throws \Exception
     */
    private function getPreviewLanguageId(): ?string
    {
        $previewLanguageId = $this->inputFilterUtil->getFilteredInput('previewLanguageId');
        if (null === $previewLanguageId) {
            return null;
        }

        // if we are not in preview mode, we don't do any further validations, because it's expensive
        $previewMode = $this->isPreviewMode();
        if (false === $previewMode) {
            return null;
        }

        // because the portal might not support the requested preview language, we need to check if the language is valid
        // since we are somewhere in the backend we need to extract the portal/language from the page
        $pageDef = $this->inputFilterUtil->getFilteredInput('pagedef');
        $page = $this->getPageService()->getById($pageDef);
        if (null === $page) {
            throw new \Exception("Unable to load requested page to determine if previewLanguageId is valid. PageDef: $pageDef, LanguageId: $previewLanguageId");
        }

        $pagePortalId = $page->fieldCmsPortalId;
        if (true === $this->isLanguageAvailableOnPortal($previewLanguageId, $pagePortalId)) {
            return $previewLanguageId;
        }

        $portal = \TdbCmsPortal::GetNewInstance();
        if (false === $portal->Load($pagePortalId)) {
            throw new \Exception("Unable to load portal of requested page to determine if previewLanguageId is valid. PageDef: $pageDef, LanguageId: $previewLanguageId");
        }

        return $portal->fieldCmsLanguageId;
    }

    /**
     * @throws Exception
     */
    private function isLanguageAvailableOnPortal(string $languageId, string $portalId): bool
    {
        $select = 'SELECT `source_id` FROM `cms_portal_cms_language_mlt` WHERE `source_id` = :portalId AND `target_id` = :languageId';
        $result = $this->databaseConnection->fetchOne($select, [
            'portalId' => $portalId,
            'languageId' => $languageId,
        ]);
        if (false === $result) {
            return false;
        }

        return true;
    }

    private function isPreviewMode(): bool
    {
        return $this->getRequestInfoService()->isPreviewMode();
    }

    /**
     * We need to load these services lazy, because they depend on the language service.
     */
    private function getPortalDomainService(): PortalDomainServiceInterface
    {
        return $this->container->get('chameleon_system_core.portal_domain_service');
    }

    private function getRequestInfoService(): RequestInfoServiceInterface
    {
        return $this->container->get('chameleon_system_core.request_info_service');
    }

    private function getActivePageService(): ActivePageServiceInterface
    {
        return $this->container->get('chameleon_system_core.active_page_service');
    }

    private function getPageService(): PageServiceInterface
    {
        return $this->container->get('chameleon_system_core.page_service');
    }

    private function getCacheService(): CacheInterface
    {
        return $this->container->get('chameleon_system_cms_cache.cache');
    }
}
