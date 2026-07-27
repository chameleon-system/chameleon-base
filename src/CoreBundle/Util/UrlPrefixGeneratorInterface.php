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
     * @return string the combined URL prefix of getLanguagePrefix() and getPortalPrefix()
     */
    public function generatePrefix(?\TdbCmsPortal $portal = null, ?\TdbCmsLanguage $language = null);

    /**
     * Returns only the language-specific part of the URL prefix, without any slashes.
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
