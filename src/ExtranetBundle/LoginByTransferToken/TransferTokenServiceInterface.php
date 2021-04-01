<?php

namespace ChameleonSystem\ExtranetBundle\LoginByTransferToken;

/**
 * Handles transfer tokens: Creates a token for the given user id that can
 * then be validated the `validateTransferToken` method.
 */
interface TransferTokenServiceInterface
{
    /**
     * Creates a transfer token for the given user id that expires after
     * the given number of seconds. The returned token must be safe for
     * usage in URLs as is.
     */
    public function createTransferTokenForUser(string $userId, int $expiresAfterSeconds): string;

    /**
     * Validates the given token as created by {@see createTransferTokenForUser}.
     * The method must return `null` if the token is invalid or the user id contained
     * in the token if it is valid. If the expiry time of a token is over, then
     * the token must be invalid.
     */
    public function getUserIdFromTransferToken(string $token): ?string;
}
