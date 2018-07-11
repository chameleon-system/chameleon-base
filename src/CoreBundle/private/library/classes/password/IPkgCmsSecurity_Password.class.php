<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * IPkgCmsSecurity_Password defines a common interface for password hashing implementations. It only supports algorithms
 * that include the salt directly in the hash (or use no hash at all, although this is of course discouraged).
 *
 * @deprecated since 6.2.0 - use chameleon_system_core.security.password.password_hash_generator instead.
 */
interface IPkgCmsSecurity_Password
{
    /**
     * Hashes a given plaintext password, using a hashing algorithm depending on the implementation.
     *
     * @param string $password
     *
     * @return string
     */
    public function hash($password);

    /**
     * Verifies that a given plaintext password and a given hash match. It is expected that implementations either know
     * which hashing algorithm is to be used, or can derive that information as well as the salt directly from the hash.
     *
     * @param string $password
     * @param string $hash
     *
     * @return bool
     */
    public function verify($password, $hash);
}
