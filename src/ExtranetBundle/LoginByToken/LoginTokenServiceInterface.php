<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ExtranetBundle\LoginByToken;

/**
 * Handles login tokens: Creates a token for the given user id that can
 * then be validated the `getUserIdFromToken` method.
 */
interface LoginTokenServiceInterface
{
    /**
     * Returns false if the service is not ready to encode or decode tokens.
     * This can be the case if certain preconditions for safety are not met.
     */
    public function isReadyToEncodeTokens(): bool;

    /**
     * Creates a token for the given user id that expires after
     * the given number of seconds. The returned token must be safe for
     * usage in URLs as is.
     */
    public function createTokenForUser(string $userId, int $expiresAfterSeconds): string;

    /**
     * Validates the given token as created by {@see createTokenForUser}.
     * The method must return `null` if the token is invalid or the user id contained
     * in the token if it is valid. If the expiry time of a token is over, then
     * the token must be invalid.
     */
    public function getUserIdFromToken(string $token): ?string;
}
