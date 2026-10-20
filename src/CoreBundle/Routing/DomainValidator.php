<?php

namespace ChameleonSystem\CoreBundle\Routing;

use ChameleonSystem\CoreBundle\Exception\InvalidPortalDomainException;
use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use ChameleonSystem\CoreBundle\Util\DomainSupportsLanguageTrait;
use Symfony\Component\HttpFoundation\RequestStack;

class DomainValidator implements DomainValidatorInterface
{
    use DomainSupportsLanguageTrait;

    public function __construct(
        private readonly PortalDomainServiceInterface $portalDomainService,
        private readonly RequestInfoServiceInterface $requestInfoService,
        private readonly RequestStack $requestStack,
        private readonly LanguageServiceInterface $languageService,
        private readonly bool $forcePrimaryDomain = CHAMELEON_FORCE_PRIMARY_DOMAIN
    ) {
    }

    /**
     * {@inheritDoc}
     *
     * @throws InvalidPortalDomainException
     */
    public function getValidDomain(
        $domain,
        ?\TdbCmsPortal $portal = null,
        ?\TdbCmsLanguage $language = null,
        $secure = true
    ) {
        if (true === $this->isDomainValid($domain, $portal, $language, $secure)) {
            return $domain;
        }

        return $this->getDefaultDomain($portal, $language, $secure);
    }

    /**
     * @throws InvalidPortalDomainException
     */
    private function isDomainValid(
        ?string $domainName,
        ?\TdbCmsPortal $portal = null,
        ?\TdbCmsLanguage $language = null,
        bool $secure = true
    ): bool {
        if (null === $domainName) {
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

            return $primaryDomainName === $domainName;
        }
        if (false === $this->requestInfoService->isBackendMode()
            && $this->isRequestingForActiveParameters($domainName, $portal, $language, $secure)) {
            return true;
        }

        return $this->isValidDomainForPortal($domainName, $portal, $language, $secure);
    }

    /**
     * Checks if the validation check is made for the same parameters as the current request. As this will be the case
     * in >95% of cases, this will improve performance by making later database calls unnecessary.
     */
    private function isRequestingForActiveParameters(
        string $domainName,
        \TdbCmsPortal $portal,
        \TdbCmsLanguage $language,
        bool $secure
    ): bool {
        $activePortal = $this->portalDomainService->getActivePortal();
        $activeDomain = $this->portalDomainService->getActiveDomain();
        $activeLanguage = $this->languageService->getActiveLanguage();
        $request = $this->requestStack->getCurrentRequest();
        $isCurrentRequestSecure = null === $request || $request->isSecure();

        return $activePortal->id === $portal->id
            && $activeLanguage->id === $language->id
            && $isCurrentRequestSecure === $secure
            && $activeDomain->GetActiveDomainName() === $domainName;
    }

    private function isValidDomainForPortal(
        string $domainName,
        \TdbCmsPortal $portal,
        \TdbCmsLanguage $language,
        bool $secure
    ): bool {
        $portalDomainList = $portal->GetFieldCmsPortalDomainsList();
        // the list pointer is shared and therefore needs a reset on usage
        $portalDomainList->GoToStart();
        while ($portalDomain = $portalDomainList->Next()) {
            $nameOfPortalDomainBeingChecked = (true === $secure) ? $portalDomain->getSecureDomainName()
                : $portalDomain->getInsecureDomainName();
            if ($domainName !== $nameOfPortalDomainBeingChecked) {
                continue;
            }

            if ($this->domainSupportsLanguage(
                $portalDomain,
                $portal,
                $language,
                $this->languageService->getCmsBaseLanguageId()
            )) {
                return true;
            }
        }

        return false;
    }

    /**
     * @throws InvalidPortalDomainException
     */
    private function getDefaultDomain(
        ?\TdbCmsPortal $portal = null,
        ?\TdbCmsLanguage $language = null,
        bool $secure = true
    ): string {
        if (null === $language) {
            $language = $this->languageService->getActiveLanguage();
        }
        if (null === $portal) {
            if ($this->requestInfoService->isBackendMode()) {
                return $this->getDefaultDomainForBackend($language);
            }
            $portal = $this->portalDomainService->getActivePortal();
        }
        if (true === $this->forcePrimaryDomain
            || null === $activeDomain = $this->portalDomainService->getActiveDomain()) {
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
     * @throws InvalidPortalDomainException
     */
    private function getDefaultDomainForBackend(\TdbCmsLanguage $language): string
    {
        if (null !== $request = $this->requestStack->getCurrentRequest()) {
            return $request->getHost();
        }

        $domain = $this->portalDomainService->getPrimaryDomain(
            $this->portalDomainService->getDefaultPortal()->id,
            $language->id
        );

        return $domain->getSecureDomainName();
    }
}
