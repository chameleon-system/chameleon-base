<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Util;

use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;

class UrlPrefixGenerator implements UrlPrefixGeneratorInterface
{
    /**
     * @var PortalDomainServiceInterface
     */
    private $portalDomainService;
    private LanguageServiceInterface $languageService;

    public function __construct(PortalDomainServiceInterface $portalDomainService, LanguageServiceInterface $languageService)
    {
        $this->portalDomainService = $portalDomainService;
        $this->languageService = $languageService;
    }

    /**
     * {@inheritdoc}
     */
    public function generatePrefixParts(
        ?\TdbCmsPortal $portal = null,
        ?\TdbCmsLanguage $language = null,
        ?\TdbCmsPortalDomains $domain = null
    ) {
        $portalPrefix = $this->getPortalPrefix($portal);
        $languagePrefix = $this->getLanguagePrefix($portal, $language, $domain);

        $prefixParts = [];
        if (!empty($portalPrefix)) {
            $prefixParts[] = $portalPrefix;
        }
        if (!empty($languagePrefix)) {
            $prefixParts[] = $languagePrefix;
        }

        return $prefixParts;
    }

    /**
     * {@inheritdoc}
     */
    public function generatePrefix(
        ?\TdbCmsPortal $portal = null,
        ?\TdbCmsLanguage $language = null,
        ?\TdbCmsPortalDomains $domain = null
    ) {
        $prefixParts = $this->generatePrefixParts($portal, $language, $domain);
        if (empty($prefixParts)) {
            return '';
        }

        return '/'.implode('/', $prefixParts);
    }

    /**
     * {@inheritdoc}
     *
     * @return string returns an empty string if either
     *                - the given portal is null
     *                - the given language is null
     *                - the given portal is not set to support multi-language
     *                - the domain associated with the portal has a language setting
     */
    public function getLanguagePrefix(
        ?\TdbCmsPortal $portal = null,
        ?\TdbCmsLanguage $language = null,
        ?\TdbCmsPortalDomains $domain = null
    ) {
        if (null === $portal) {
            return '';
        }

        if (null === $language) {
            return '';
        }

        if (false === $portal->fieldUseMultilanguage) {
            return '';
        }

        if (null !== $domain && [] !== $domain->GetFieldCmsLanguageIdList()) {
            $defaultLanguageId = $domain->fieldCmsLanguageId;
            if ('' === $defaultLanguageId) {
                $defaultLanguageId = $portal->fieldCmsLanguageId;
            }
            if ('' === $defaultLanguageId) {
                $defaultLanguageId = $this->languageService->getCmsBaseLanguageId();
            }

            return $defaultLanguageId === $language->id ? '' : $language->fieldIso6391;
        }

        $primaryTargetDomain = $this->portalDomainService->getPrimaryDomain($portal->id, $language->id);
        if ($primaryTargetDomain->fieldCmsLanguageId === $language->id) {
            return '';
        }

        if ('' === $portal->fieldCmsLanguageId || $portal->fieldCmsLanguageId === $language->id) {
            return '';
        }

        return $language->fieldIso6391;
    }

    /**
     * {@inheritdoc}
     */
    public function getPortalPrefix(?\TdbCmsPortal $portal = null)
    {
        if (null === $portal) {
            return '';
        }

        return $portal->fieldIdentifier;
    }
}
