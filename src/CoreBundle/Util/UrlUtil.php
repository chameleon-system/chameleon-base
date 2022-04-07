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

use ChameleonSystem\CoreBundle\Routing\DomainValidatorInterface;
use ChameleonSystem\CoreBundle\Security\AuthenticityToken\AuthenticityTokenManagerInterface;
use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use TdbCmsLanguage;
use TdbCmsPortal;

class UrlUtil
{
    /**
     * @var UrlPrefixGeneratorInterface
     */
    private $urlPrefixGenerator;
    /**
     * @var PortalDomainServiceInterface
     */
    private $portalDomainService;
    /**
     * @var LanguageServiceInterface
     */
    private $languageService;
    /**
     * @var DomainValidatorInterface
     */
    private $domainValidator;
    /**
     * @var AuthenticityTokenManagerInterface
     */
    private $authenticityTokenManager;

    /**
     * @param UrlPrefixGeneratorInterface       $urlPrefixGenerator
     * @param PortalDomainServiceInterface      $portalDomainService
     * @param LanguageServiceInterface          $languageService
     * @param DomainValidatorInterface          $domainValidator
     * @param AuthenticityTokenManagerInterface $authenticityTokenManager
     */
    public function __construct(
        UrlPrefixGeneratorInterface $urlPrefixGenerator,
        PortalDomainServiceInterface $portalDomainService,
        LanguageServiceInterface $languageService,
        DomainValidatorInterface $domainValidator,
        AuthenticityTokenManagerInterface $authenticityTokenManager
    ) {
        $this->urlPrefixGenerator = $urlPrefixGenerator;
        $this->portalDomainService = $portalDomainService;
        $this->languageService = $languageService;
        $this->domainValidator = $domainValidator;
        $this->authenticityTokenManager = $authenticityTokenManager;
    }

    /**
     * converts an array (even multidimensional) to URL parameters.
     *
     * @param array  $data
     * @param string $prefix
     * @param string $paramSeparator
     *
     * @return string
     */
    public function getArrayAsUrl(array $data, $prefix = '', $paramSeparator = '&amp;')
    {
        $this->removeAuthenticityTokenFromArray($data);
        /**
         * @psalm-suppress NullArgument
         * @FIXME Passing `null` as prefix works but triggers a warning in PHP8.1. It behaves the same as passing an empty string.
         */
        $url = $prefix.http_build_query($data, null, $paramSeparator);

        /*
         * Add authenticity token without URL encoding
         */
        $this->addAuthenticityTokenToUrlStringIfRequired($url, $data, $paramSeparator);

        return $url;
    }

    /**
     * @param string $url
     *
     * @return array
     */
    public function getUrlParametersAsArray($url)
    {
        $retValue = array();
        if (false !== $parameterStartPos = strpos($url, '?')) {
            $url = substr($url, $parameterStartPos + 1);
        }
        if ('' === $url) {
            return $retValue;
        }
        $singleParameterList = explode('&', $url);
        foreach ($singleParameterList as $singleParameter) {
            if (1 !== substr_count($singleParameter, '=')) {
                continue;
            }
            list($key, $value) = explode('=', $singleParameter);
            $retValue[$key] = $value;
        }

        return $retValue;
    }

    /**
     * Returns the URL of the current request. The URL can be modified by specifying which protocol to use (HTTP/HTTPS)
     * and/or passing additional URL parameters and/or specifying which URL parameters to remove.
     *
     * @param Request     $request
     * @param string|null $protocol
     * @param array       $parameterBlacklist
     * @param array       $additionalParameters
     *
     * @return string|null
     */
    public function getModifiedUrlFromRequest(
        Request $request,
        $protocol = null,
        array $parameterBlacklist = array(),
        $additionalParameters = array()
    ) {
        $requestUri = $request->getRequestUri();

        if (null === $protocol || false === in_array($protocol, array('http', 'https'), true)) {
            $requestedScheme = $request->getScheme();
        } else {
            $requestedScheme = $protocol;
        }
        $transformedUrl = "$requestedScheme://";

        $domain = $this->domainValidator->getValidDomain(
            $request->getHost(),
            null,
            null,
            'https' === $protocol);
        $transformedUrl .= $domain;

        $port = $request->getPort();
        $currentScheme = $request->getScheme();
        if (($currentScheme === $requestedScheme) && (false === $this->isDefaultPort($port, $currentScheme))) {
            $transformedUrl .= ':'.$port;
        }
        $transformedUrl .= $requestUri;

        if (null === $request->getQueryString() && $request->query->count() > 0) {
            $queryParam = $request->query->all();
            foreach ($queryParam as $key => $value) {
                if (in_array($key, $parameterBlacklist)) {
                    unset($queryParam[$key]);
                }
            }

            foreach ($additionalParameters as $key => $value) {
                $queryParam[$key] = $value;
            }

            if (count($queryParam) > 0) {
                $transformedUrl .= $this->getArrayAsUrl($queryParam, '?', '&');
            }
        }

        return $transformedUrl;
    }

    /**
     * @param int    $port
     * @param string $protocol
     *
     * @return bool
     */
    private function isDefaultPort($port, $protocol)
    {
        return ('http' === $protocol && 80 === $port) || ('https' === $protocol && 443 === $port);
    }

    /**
     * @param string $url
     *
     * @return bool
     */
    public function isUrlAbsolute($url)
    {
        return
               (0 === strpos($url, 'http://'))
            || (0 === strpos($url, 'https://'))
            || (0 === strpos($url, '//'))
        ;
    }

    /**
     * @param string $url
     *
     * @return bool
     */
    public function isUrlSecure($url)
    {
        return 0 === strpos($url, 'https://');
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public function getHostFromUrl($url)
    {
        if (preg_match('#^http(s)?:\/\/([^\/:]+)#', $url, $matches)) {
            $url = $matches[2];
        }

        return $url;
    }

    /**
     * @param string         $url
     * @param TdbCmsPortal   $portal
     * @param TdbCmsLanguage $language
     *
     * @return string
     */
    public function normalizeURL($url, TdbCmsPortal $portal, TdbCmsLanguage $language)
    {
        $url = $this->cutPortalAndLanguagePrefixFromUrl($url, $portal, $language);

        $pagePath = mb_strtolower($url);

        if ('.html' === substr($pagePath, -5)) {
            $pagePath = substr($pagePath, 0, -5);
        }
        if ('.htm' === substr($pagePath, -4)) {
            $pagePath = substr($pagePath, 0, -4);
        }

        while (strlen($pagePath) > 1 && '/' === substr($pagePath, -1)) {
            $pagePath = substr($pagePath, 0, -1);
        }
        if ('/' !== substr($pagePath, 0, 1)) {
            $pagePath = '/'.$pagePath;
        }
        if ('' === $pagePath) {
            $pagePath = '/';
        }

        $pagePath = urldecode($pagePath);

        return $pagePath;
    }

    /**
     * @param string         $url
     * @param TdbCmsPortal   $portal
     * @param TdbCmsLanguage $language
     *
     * @return string
     */
    public function cutPortalAndLanguagePrefixFromUrl($url, TdbCmsPortal $portal, TdbCmsLanguage $language)
    {
        $urlPrefixGenerator = $this->urlPrefixGenerator;
        $prefixToCutParts = $urlPrefixGenerator->generatePrefixParts($portal, $language);

        if (0 === count($prefixToCutParts)) {
            return $url;
        }
        $prefixToCut = implode('/', $prefixToCutParts).'/';
        $urlBefore = $url;
        $url = $this->cutPrefixFromString($url, $prefixToCut);
        if (strlen($url) === strlen($urlBefore)) {
            $prefixToCut = '/'.$prefixToCut;
            $url = $this->cutPrefixFromString($url, $prefixToCut);
        }

        return $url;
    }

    /**
     * Returns the passed $string, shortened by the $prefixToCut if it occurs at the start of $string.
     * Otherwise, the $string is returned unchanged.
     *
     * @param string $string
     * @param string $prefixToCut
     *
     * @return string
     */
    private function cutPrefixFromString($string, $prefixToCut)
    {
        if (0 === strpos($string, $prefixToCut)) {
            if (strlen($string) === strlen($prefixToCut)) {
                $string = '';
            } else {
                $string = substr($string, strlen($prefixToCut));
            }
        }

        return $string;
    }

    /**
     * Returns a relative URL for a given absolute URL. If a relative URL is passed, it will not be changed.
     *
     * @param string $url
     *
     * @return string
     */
    public function getRelativeUrl($url)
    {
        if (false === $this->isUrlAbsolute($url)) {
            return $url;
        }
        if (preg_match('#^http(s)?:\/\/([^\/:]+)(.*)#', $url, $matches)) {
            $url = $matches[3];
        }

        return $url;
    }

    /**
     * Returns an absolute URL for a given relative URL. If an absolute URL is passed, it may be changed if
     * other the other parameters require changes (the domain depends on portal, language and if the URL is secure
     * a.k.a. HTTPS).
     * If $portal and/or $language are not set, the currently active values for the request will be used.
     * If $domain is not set and $url is absolute, the domain will be taken from $url if this domain is valid.
     * Otherwise the active or primary domain is used, depending on Chameleon settings.
     *
     * @param string              $url
     * @param bool                $secure
     * @param string|null         $domain
     * @param TdbCmsPortal|null   $portal
     * @param TdbCmsLanguage|null $language
     *
     * @return string
     */
    public function getAbsoluteUrl(
        $url,
        $secure,
        $domain = null,
        TdbCmsPortal $portal = null,
        TdbCmsLanguage $language = null
    ) {
        if (true === $this->isUrlAbsolute($url)) {
            /*
             * Make the URL relative and then absolute again,
             * because we can't know if scheme and domain fit the passed portal, language, and secure state
             */
            if (null === $domain) {
                $domain = $this->getHostFromUrl($url);
            }
            $url = $this->getRelativeUrl($url);
        }

        if (0 !== strpos($url, '/')) {
            $url = '/'.$url;
        }

        $scheme = $secure ? 'https://' : 'http://';

        if (null === $portal) {
            $portal = $this->portalDomainService->getActivePortal();
        }

        if (null === $language) {
            $language = $this->languageService->getActiveLanguage();
        }

        $domain = $this->domainValidator->getValidDomain($domain, $portal, $language, $secure);

        return $scheme.$domain.$url;
    }

    /**
     * @param array $parameters
     */
    public function addAuthenticityTokenToArrayIfRequired(array &$parameters)
    {
        $this->removeAuthenticityTokenFromArray($parameters);
        if ($this->isAuthenticityTokenRequired($parameters)) {
            $tokenId = AuthenticityTokenManagerInterface::TOKEN_ID;
            $parameters[$tokenId] = "[{{$tokenId}}]";
        }
    }

    /**
     * @param array $parameters
     *
     * @return bool
     */
    private function isAuthenticityTokenRequired(array $parameters)
    {
        if (false === $this->authenticityTokenManager->isProtectionEnabled()) {
            return false;
        }
        $foundModuleFnc = isset($parameters['module_fnc']);
        if (!$foundModuleFnc) {
            foreach ($parameters as $key => $value) {
                if ('module_fnc[' === substr($key, 0, 11)) {
                    $foundModuleFnc = true;
                    break;
                }
            }
        }
        if (!$foundModuleFnc) {
            return false;
        }

        return true;
    }

    /**
     * @param string $url
     * @param array  $parameters
     * @param string $paramSeparator
     */
    public function addAuthenticityTokenToUrlStringIfRequired(&$url, array $parameters, $paramSeparator = '&amp;')
    {
        $this->removeAuthenticityTokenFromArray($parameters);
        if (!$this->isAuthenticityTokenRequired($parameters)) {
            return;
        }
        if (false === strpos($url, '=')) {
            $separator = '?';
        } else {
            $separator = $paramSeparator;
        }
        $url .= $separator.$this->authenticityTokenManager->getTokenPlaceholderAsParameter();
    }

    /**
     * @param array $parameters
     */
    public function removeAuthenticityTokenFromArray(array &$parameters)
    {
        unset($parameters[AuthenticityTokenManagerInterface::TOKEN_ID]);
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public function removeAuthenticityTokenFromUrl($url)
    {
        if (false === $paramStartPos = strpos($url, '?')) {
            return $url;
        }
        $params = explode('&', substr($url, $paramStartPos + 1));
        $authenticityTokenString = $this->authenticityTokenManager->getTokenPlaceholderAsParameter();

        foreach ($params as $index => $param) {
            if ($param === $authenticityTokenString) {
                unset($params[$index]);
                break;
            }
        }
        $baseUrl = substr($url, 0, $paramStartPos);
        if (0 === count($params)) {
            return $baseUrl;
        }

        return  $baseUrl.'?'.implode('&', $params);
    }

    /**
     * This method receives a URL(absolute or relative) and forces its
     * path elements (nothing else!) to be wellformed using urlencode().
     * This ensures HTML validators will not fail on external URLs.
     *
     * @var $url string
     *
     * @return string
     */
    public function encodeUrlParts($url)
    {
        $urlPath = parse_url($url, PHP_URL_PATH);
        $urlPathComponents = explode('/', $urlPath);
        foreach ($urlPathComponents as $index => $component) {
            $urlPathComponents[$index] = urlencode(trim($urlPathComponents[$index]));
        }
        $linkPathSanitized = implode('/', $urlPathComponents);
        $linkPathSanitized = str_replace('%2B', '+', $linkPathSanitized);

        return str_replace($urlPath, $linkPathSanitized, $url);
    }
}
