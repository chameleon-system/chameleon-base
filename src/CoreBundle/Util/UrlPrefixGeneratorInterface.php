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

/**
 * Interface UrlPrefixGeneratorInterface defines a service that generates prefixes which are part of every
 * Chameleon URL, but not part of the route. By default this prefix contains information on a given portal and
 * given language.
 */
interface UrlPrefixGeneratorInterface
{
    /**
     * @return string[]
     */
    public function generatePrefixParts(?\TdbCmsPortal $portal = null, ?\TdbCmsLanguage $language = null);

    /**
     * Generates the complete URL prefix, including a leading slash and slashes as separators between the different
     * URL parts. The result is of varying length, depending on if the prefix parts are needed.
     *
     * @return string the combined URL prefix of getUrlLanguagePrefix() and getPortalPrefix()
     */
    public function generatePrefix(?\TdbCmsPortal $portal = null, ?\TdbCmsLanguage $language = null);

    /**
     * Generates the complete URL prefix for a concrete domain variant.
     *
     * If no domain is passed, implementations should fall back to the default/primary domain behavior of generatePrefix().
     *
     * @return string the combined URL prefix of the given portal and language for the passed domain variant
     */
    public function generatePrefixForDomain(?\TdbCmsPortal $portal = null, ?\TdbCmsLanguage $language = null, ?\TdbCmsPortalDomains $domain = null);

    /**
     * Generates the complete URL path prefix for a concrete portal/language/domain combination.
     *
     * The path prefix consists of the optional portal suffix followed by the optional domain-language suffix.
     *
     * @return string the combined path prefix including a leading slash, or an empty string if no prefix segments are needed
     */
    public function getPathPrefix(?\TdbCmsPortal $portal = null, ?\TdbCmsLanguage $language = null, ?\TdbCmsPortalDomains $domain = null);

    /**
     * Returns the domain-language-specific path segment of the URL prefix, without any slashes.
     *
     * @return string the configured domain URL suffix, the legacy language prefix, or an empty string (depending on the implementation)
     */
    public function getDomainLanguagePathSegment(?\TdbCmsPortal $portal = null, ?\TdbCmsLanguage $language = null, ?\TdbCmsPortalDomains $domain = null);

    /**
     * Returns the URL-specific language part of the prefix, without any slashes.
     *
     * @return string the configured domain URL suffix or legacy language prefix of the given language, or an empty string (depending on the implementation)
     */
    public function getUrlLanguagePrefix(?\TdbCmsPortal $portal = null, ?\TdbCmsLanguage $language = null);

    /**
     * Returns the legacy language-specific part of the URL prefix, without any slashes.
     *
     * @return string the language ISO6391 code of the given language, or an empty string (depending on the implementation)
     */
    public function getLanguagePrefix(?\TdbCmsPortal $portal = null, ?\TdbCmsLanguage $language = null);

    /**
     * Returns only the portal-specific part of the URL prefix, without any slashes.
     *
     * @return string the portal prefix as defined in the portal backend configuration, or an empty string (depending on the implementation)
     */
    public function getPortalPrefix(?\TdbCmsPortal $portal = null);
}
