<?php

namespace ChameleonSystem\CoreBundle\Routing;

/**
 * DomainValidatorInterface defines a service that ensures that a given domain is valid for the context where it should
 * be used.
 */
interface DomainValidatorInterface
{
    /**
     * Ensures that $domain is valid for the passed $portal, $language, and HTTPS/HTTP attribute $secure.
     * If the domain is valid for these parameters, it will be returned unchanged. If it is not valid, another domain
     * will be returned that - although possibly depending on many factors - is in any case valid and can directly be
     * used to generate absolute URLs.
     *
     * @param string|null $domain the domain to check
     * @param \TdbCmsPortal|null $portal defaults to the active portal if not passed
     * @param \TdbCmsLanguage|null $language defaults to the active language if not passed
     * @param bool $secure if true, the $domain is checked against secure domains of $portal, else against the default
     *                     domain
     *
     * @return string|null
     */
    public function getValidDomain($domain, ?\TdbCmsPortal $portal = null, ?\TdbCmsLanguage $language = null, $secure = true);
}
