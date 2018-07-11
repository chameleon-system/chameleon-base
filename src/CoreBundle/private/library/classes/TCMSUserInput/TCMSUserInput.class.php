<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Security\AuthenticityToken\AuthenticityTokenManagerInterface;
use ChameleonSystem\CoreBundle\Security\AuthenticityToken\InvalidTokenFormatException;
use ChameleonSystem\CoreBundle\Security\AuthenticityToken\TokenInjectionFailedException;
use ChameleonSystem\CoreBundle\ServiceLocator;

/**
 * manages all user input.
/**/
class TCMSUserInput
{
    const FILTER_NONE = '';
    const FILTER_DEFAULT = 'TCMSUserInput_BaseText';
    const FILTER_SAFE_TEXT = 'TCMSUserInput_SafeText';
    const FILTER_SAFE_TEXTBLOCK = 'TCMSUserInput_SafeTextBlock';
    const FILTER_INT = 'TCMSUserInput_Int';
    const FILTER_DECIMAL = 'TCMSUserInput_Decimal';
    /**
     * @deprecated since 6.0.12 - it makes no sense to silently change/filter any characters in an email address. Invalid
     * characters should lead to an error message and might additionally be filtered by FILTER_DEFAULT to ensure that
     * no characters that are dangerous for the system remain.
     */
    const FILTER_EMAIL = 'TCMSUserInput_EMail';
    const FILTER_FILENAME = 'TCMSUserInput_Filename';
    const FILTER_DATE = 'TCMSUserInput_Date';
    const FILTER_URL = 'TCMSUserInput_URL';
    /**
     * @deprecated since 6.2.0 - user input needs to be escaped depending on the target system/language. Use Twig
     * escaping when rendering to HTML.
     */
    const FILTER_XSS = 'TCMSUserInput_XSS';
    const FILTER_PASSWORD = 'TCMSUserInput_Password';
    const FILTER_URL_INTERNAL = 'TCMSUserInput_InternalURL';

    /**
     * return the filtered value.
     *
     * @param $sValue
     * @param $sFilterClass - form: classname;path;type|classname;path;type
     *
     * @return string
     *
     * @deprecated use chameleon_system_core.util.input_filter::filterValue() instead
     */
    public static function FilterValue($sValue, $sFilterClass)
    {
        static $aFilteredValueCache = array();
        $sCacheKey = '';
        if (is_array($sValue)) {
            $sCacheKey = md5(serialize($sValue));
        } else {
            $sCacheKey = md5($sValue);
        }

        $sCacheKey = $sFilterClass.'-'.$sCacheKey;
        if (array_key_exists($sCacheKey, $aFilteredValueCache)) {
            return $aFilteredValueCache[$sCacheKey];
        }

        $aFilters = self::GetFilterObject($sFilterClass);
        /** @var $oFilter TCMSUserInput_Raw */
        foreach ($aFilters as $oFilter) {
            $sValue = $oFilter->Filter($sValue);
        }
        $aFilteredValueCache[$sCacheKey] = $sValue;

        return $sValue;
    }

    /**
     * return a array that holds x filter objects of TCMSUserInputFilter or the subclasses.
     *
     * @param string $sFilterClass - form: classname;path;type|classname;path;type
     *
     * @return array
     *
     * @deprecated use chameleon_system_core.util.input_filter::getFilterObject() instead
     */
    public static function GetFilterObject($sFilterClass)
    {
        $aFilters = array();
        $aFilterClasses = explode('|', $sFilterClass);
        foreach ($aFilterClasses as $sFilter) {
            $aParts = explode(';', $sFilter);
            $sClassName = $aParts[0];
            $aFilters[] = new $sClassName();
        }

        return $aFilters;
    }

    /**
     * generate a new authenticity token in the session. the method will be called when starting
     * a new session, or when a user logs in/out.
     *
     * @deprecated since 6.2.0 - use \ChameleonSystem\CoreBundle\Security\AuthenticityToken\AuthenticityTokenManagerInterface::refreshToken()
     *             instead.
     */
    public static function GenerateNewAuthenticityToken()
    {
        self::getAuthenticityTokenManager()->refreshToken();
    }

    /**
     * return the current token - or false if none has been set yet.
     *
     * @return string
     *
     * @deprecated since 6.2.0 - use \ChameleonSystem\CoreBundle\Security\AuthenticityToken\AuthenticityTokenManagerInterface::getStoredToken()
     *             instead.
     */
    public static function GetAuthenticityToken()
    {
        return self::getAuthenticityTokenManager()->getStoredToken();
    }

    /**
     * checks input to see if it contains the authenticity token - and if so, if the token is valid.
     *
     * @return bool
     *
     * @deprecated since 6.2.0 - use \ChameleonSystem\CoreBundle\Security\AuthenticityToken\AuthenticityTokenManagerInterface::isTokenValid()
     *             instead.
     */
    public static function CheckAuthenticityTokenInInput()
    {
        return self::getAuthenticityTokenManager()->isTokenValid();
    }

    /**
     * return the input parameter for the token (get/post).
     *
     * @param string $sFormat       - specify if you want the toke via get, post or as an array
     * @param bool   $bUseRealToken - set to true, if you want to return the token - not just the token var that needs to be replaced by the controller
     *
     * @return string
     *
     * @deprecated since 6.2.0 - use \ChameleonSystem\CoreBundle\Security\AuthenticityToken\AuthenticityTokenManagerInterface::getTokenPlaceholderAsParameter()
     *             or \ChameleonSystem\CoreBundle\Security\AuthenticityToken\AuthenticityTokenManagerInterface::getResolvedTokenAsParameter()
     *             instead.
     */
    public static function GetAuthenticityTokenString($sFormat = 'get', $bUseRealToken = false)
    {
        $authenticityTokenManager = self::getAuthenticityTokenManager();
        if (true === $bUseRealToken) {
            try {
                return $authenticityTokenManager->getResolvedTokenAsParameter($sFormat);
            } catch (InvalidTokenFormatException $e) {
                return $authenticityTokenManager->getResolvedTokenAsParameter(AuthenticityTokenManagerInterface::TOKEN_FORMAT_ARRAY);
            }
        }

        try {
            return $authenticityTokenManager->getTokenPlaceholderAsParameter($sFormat);
        } catch (InvalidTokenFormatException $e) {
            return $authenticityTokenManager->getTokenPlaceholderAsParameter(AuthenticityTokenManagerInterface::TOKEN_FORMAT_ARRAY);
        }
    }

    /**
     * return the url parameter name for the token.
     *
     * @return string
     *
     * @deprecated since 6.2.0 - use AuthenticityTokenManagerInterface::TOKEN_ID instead.
     */
    public static function GetAuthenticityTokenName()
    {
        return AuthenticityTokenManagerInterface::TOKEN_ID;
    }

    /**
     * add authenticity token to all forms in the string.
     *
     * @param string $sString
     *
     * @return string
     *
     * @deprecated since 6.2.0 - use \ChameleonSystem\CoreBundle\Security\AuthenticityToken\AuthenticityTokenManagerInterface::addTokenToForms()
     *             instead.
     */
    public static function AutoAddAuthenticityTokenToForms($sString)
    {
        try {
            return self::getAuthenticityTokenManager()->addTokenToForms($sString);
        } catch (TokenInjectionFailedException $e) {
            trigger_error('Error in preg_replace code. Authenticity token NOT added', E_USER_WARNING);

            return $sString;
        }
    }

    /**
     * add the authenticity token to all forms. the hook is called by the controller.
     *
     * @deprecated since 6.2.0 - no longer used.
     */
    public static function CallbackAddAuthenticityTokenToForms($aMatches)
    {
        return $aMatches[0].self::getAuthenticityTokenManager()
                ->getResolvedTokenAsParameter(AuthenticityTokenManagerInterface::TOKEN_FORMAT_POST);
    }

    /**
     * return true if all request to module_fnc should be auto protected.
     *
     * @return bool
     *
     * @deprecated since 6.2.0 - use \ChameleonSystem\CoreBundle\Security\AuthenticityToken\AuthenticityTokenManagerInterface::isProtectionEnabled()
     *             instead.
     */
    public static function HasActiveAutoProtectInputViaAuthenticityToken()
    {
        return self::getAuthenticityTokenManager()->isProtectionEnabled();
    }

    /**
     * @return AuthenticityTokenManagerInterface
     */
    private static function getAuthenticityTokenManager()
    {
        return ServiceLocator::get('chameleon_system_core.security.authenticity_token.authenticity_token_manager');
    }
}
