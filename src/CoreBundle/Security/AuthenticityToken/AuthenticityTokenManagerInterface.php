<?php

namespace ChameleonSystem\CoreBundle\Security\AuthenticityToken;

/**
 * Defines a service that is responsible for handling the authenticity/CSRF token.
 */
interface AuthenticityTokenManagerInterface
{
    public const TOKEN_ID = 'cmsauthenticitytoken';

    public const TOKEN_FORMAT_GET = 0;
    public const TOKEN_FORMAT_POST = 1;
    public const TOKEN_FORMAT_ARRAY = 2;

    /**
     * Returns true if the protection by authenticity token is enabled. Implementations may introduce fancy handling
     * such as distinction between frontend and backend.
     *
     * @return bool
     */
    public function isProtectionEnabled();

    /**
     * Returns true if the authenticity token submitted by the user is valid. If no token was submitted, this method
     * returns false. If isProtectionEnabled() returns false, this method always returns true.
     *
     * @return bool
     */
    public function isTokenValid();

    /**
     * Generates and stores a new authenticity token.
     *
     * @return void
     */
    public function refreshToken();

    /**
     * Returns the currently stored token. If no token is stored, a new one will be generated and stored, so
     * that this method will always return a valid token.
     *
     * @return string
     */
    public function getStoredToken();

    /**
     * Adds the authenticity token to all forms that have a module_fnc parameter.
     *
     * @param string $string
     *
     * @return string
     *
     * @throws TokenInjectionFailedException if the token could not be set
     */
    public function addTokenToForms($string);

    /**
     * Returns the token ID and placeholder in one of different formats (one of the constants in this interface).
     * The placeholder is handy when caching results, so that the actual token value can be injected individually for
     * each user after retrieving the string from the cache.
     *
     * @param int $format
     *
     * @psalm-param self::TOKEN_* $format
     *
     * @return array|string
     *
     * @throws InvalidTokenFormatException
     */
    public function getTokenPlaceholderAsParameter($format = self::TOKEN_FORMAT_GET);

    /**
     * Returns the token ID and authenticity token in one of different formats (one of the constants in this interface).
     *
     * @param int $format
     *
     * @psalm-param self::TOKEN_* $format
     *
     * @return array|string
     *
     * @throws InvalidTokenFormatException
     */
    public function getResolvedTokenAsParameter($format = self::TOKEN_FORMAT_GET);
}
