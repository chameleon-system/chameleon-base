<?php

namespace ChameleonSystem\CoreBundle\Routing;

use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class DomainValidator implements DomainValidatorInterface
{
    /**
     * @var PortalDomainServiceInterface
     */
    private $portalDomainService;
    /**
     * @var RequestInfoServiceInterface
     */
    private $requestInfoService;
    /**
     * @var bool
     */
    private $forcePrimaryDomain;
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var LanguageServiceInterface
     */
    private $languageService;

    /**
     * @param bool $forcePrimaryDomain
     */
    public function __construct(PortalDomainServiceInterface $portalDomainService, RequestInfoServiceInterface $requestInfoService, RequestStack $requestStack, LanguageServiceInterface $languageService, $forcePrimaryDomain = CHAMELEON_FORCE_PRIMARY_DOMAIN)
    {
        $this->portalDomainService = $portalDomainService;
        $this->requestInfoService = $requestInfoService;
        $this->requestStack = $requestStack;
        $this->languageService = $languageService;
        $this->forcePrimaryDomain = $forcePrimaryDomain;
    }

    /**
     * {@inheritdoc}
     */
    public function getValidDomain($domain, ?\TdbCmsPortal $portal = null, ?\TdbCmsLanguage $language = null, $secure = true)
    {
        if (true === $this->isDomainValid($domain, $portal, $language, $secure)) {
            return $domain;
        }

        return $this->getDefaultDomain($portal, $language, $secure);
    }

    /**
     * @param string|null $domain
     * @param bool $secure
     *
     * @return bool
     */
    private function isDomainValid($domain, ?\TdbCmsPortal $portal = null, ?\TdbCmsLanguage $language = null, $secure = true)
    {
        if (null === $domain) {
            return false;
        }
        if (null === $portal) {
            if ($this->requestInfoService->isBackendMode()) {
                return true; // allow all domains while in backend mode if no specific portal was requested.
            }

            $portal = $this->portalDomainService->getActivePortal();
        }

        if (null === $language) {
            $language = $this->languageService->getActiveLanguage();
        }
        if ($this->forcePrimaryDomain) {
            $primaryDomain = $this->portalDomainService->getPrimaryDomain($portal->id, $language->id);
            if (true === $secure) {
                $primaryDomainName = $primaryDomain->getSecureDomainName();
            } else {
                $primaryDomainName = $primaryDomain->getInsecureDomainName();
            }

            return $primaryDomainName === $domain;
        }
        if (false === $this->requestInfoService->isBackendMode()
            && $this->isRequestingForActiveParameters($domain, $portal, $language, $secure)) {
            return true;
        }

        return $this->isValidDomainForPortal($domain, $portal, $language, $secure);
    }

    /**
     * Checks if the validation check is made for the same parameters as the current request. As this will be the case
     * in >95% of cases, this will improve performance by making subsequent database calls unnecessary.
     *
     * @param string $domain
     * @param bool $secure
     *
     * @return bool
     */
    private function isRequestingForActiveParameters($domain, \TdbCmsPortal $portal, \TdbCmsLanguage $language, $secure)
    {
        $activePortal = $this->portalDomainService->getActivePortal();
        $activeDomain = $this->portalDomainService->getActiveDomain();
        $activeLanguage = $this->languageService->getActiveLanguage();
        $request = $this->requestStack->getCurrentRequest();
        $isCurrentRequestSecure = null === $request || $request->isSecure();

        return $activePortal->id === $portal->id
            && $activeLanguage->id === $language->id
            && $isCurrentRequestSecure === $secure
            && $activeDomain->GetActiveDomainName() === $domain
        ;
    }

    /**
     * @param string $domain
     * @param bool $secure
     *
     * @return bool
     */
    private function isValidDomainForPortal($domain, \TdbCmsPortal $portal, \TdbCmsLanguage $language, $secure)
    {
        $portalLanguageIds = $portal->GetFieldCmsLanguageIdList();
        $portalDefaultLanguageId = '' !== $portal->fieldCmsLanguageId ? $portal->fieldCmsLanguageId : \TdbCmsConfig::GetInstance()->fieldTranslationBaseLanguageId;
        $portalDomainList = $portal->GetFieldCmsPortalDomainsList();

        $domainWithoutLanguageCandidates = [];
        while($portalDomain = $portalDomainList->Next()) {
            // make sure the domain matches
            $nameOfPortalDomainBeingChecked = (true === $secure) ? $portalDomain->getSecureDomainName() : $portalDomain->getInsecureDomainName();
            if ($domain !== $nameOfPortalDomainBeingChecked) {
                continue;
            }

            // now check if the language of the domain matches.

            // directly assigned to additional language?
            $validDomainLanguages = true === $portal->fieldUseMultilanguage ? $portalDomain->GetFieldCmsLanguageIdList() : [];
            if (in_array($language->id, $validDomainLanguages, true)) {
                return true;
            }

            // directly assigned to the domain
            if ($language->id === $portalDomain->fieldCmsLanguageId) {
                return true;
            }
            // if the current language is the default language and the domain has no active language
            if ($portalDefaultLanguageId === $language->id && '' === $portalDomain->fieldCmsLanguageId) {
                $domainWithoutLanguageCandidates[] = $portalDomain;
                continue;
            }
            /**
             * if the current language is a supported portal language, and the domain has no language and no additional languages
             * Example:
             *
             * - Portal default: de
             * - Portal languages: [de, en]
             * - Domain: no direct language and no additional languages
             * - Requested language: en
             */
            if (''===$portalDomain->fieldCmsLanguageId && [] === $validDomainLanguages && in_array($language->id, $portalLanguageIds, true)) {
                $domainWithoutLanguageCandidates[] = $portalDomain;
                continue;
            }
        }

        // other domain candidates that do not have a direct language match but are valid for other reasons
        if ([] !== $domainWithoutLanguageCandidates) {
            return true;
        }

        return false;
    }

    /**
     * @param bool $secure
     *
     * @return string
     */
    private function getDefaultDomain(?\TdbCmsPortal $portal = null, ?\TdbCmsLanguage $language = null, $secure = true)
    {
        if (null === $language) {
            $language = $this->languageService->getActiveLanguage();
        }
        if (null === $portal) {
            if ($this->requestInfoService->isBackendMode()) {
                return $this->getDefaultDomainForBackend($language);
            }
            $portal = $this->portalDomainService->getActivePortal();
        }
        if (true === $this->forcePrimaryDomain || null === $activeDomain = $this->portalDomainService->getActiveDomain()) {
            $domain = $this->portalDomainService->getPrimaryDomain($portal->id, $language->id);

            return true === $secure ? $domain->getSecureDomainName() : $domain->getInsecureDomainName();
        }

        $domainName = $activeDomain->GetActiveDomainName();

        if ($this->isDomainValid($domainName, $portal, $language, $secure)) {
            return $domainName;
        }

        $domain = $this->portalDomainService->getPrimaryDomain($portal->id, $language->id);

        return true === $secure ? $domain->getSecureDomainName() : $domain->getInsecureDomainName();
    }

    /**
     * @return string
     */
    private function getDefaultDomainForBackend(\TdbCmsLanguage $language)
    {
        if (null !== $request = $this->requestStack->getCurrentRequest()) {
            return $request->getHost();
        }

        $domain = $this->portalDomainService->getPrimaryDomain($this->portalDomainService->getDefaultPortal()->id, $language->id);

        return $domain->getSecureDomainName();
    }
}
