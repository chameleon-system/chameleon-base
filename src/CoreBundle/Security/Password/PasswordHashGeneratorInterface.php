<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Security\Password;

/**
 * PasswordHashGeneratorInterface defines a service that can be used for common password hashing operations.
 * The interface does not specify ways to specify the used hashing algorithm. This depends on the implementation. The
 * same service should be used throughout the system to ensure compatibility.
 */
interface PasswordHashGeneratorInterface
{
    public const MAXIMUM_PASSWORD_LENGTH = 1000;

    /**
     * Returns a hashed password.
     *
     * @param string $plainPassword
     *
     * @return string
     */
    public function hash($plainPassword);

    /**
     * Returns true if $hash can be verified as valid hash for $plainPassword.
     *
     * @param string $plainPassword
     * @param string $hash
     *
     * @return bool
     */
    public function verify($plainPassword, $hash);

    /**
     * Returns true if $hash should be re-hashed because of a changed algorithm, changed algorithms settings or other
     * reasons controlled by the implementation.
     *
     * @param string $hash
     *
     * @return bool
     */
    public function needsRehash($hash);
}
