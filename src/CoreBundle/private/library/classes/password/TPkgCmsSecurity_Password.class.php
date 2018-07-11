<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Security\Password\PasswordHashGeneratorInterface;

/**
 * TPkgCmsSecurity_Password is an implementation of IPkgCmsSecurity_Password that uses the BCrypt algorithm for hashing.
 *
 * @deprecated since 6.2.0 - use chameleon_system_core.security.password.password_hash_generator instead.
 */
class TPkgCmsSecurity_Password implements IPkgCmsSecurity_Password
{
    /**
     * @var PasswordHashGeneratorInterface
     */
    private $passwordHashGenerator;

    /**
     * @param PasswordHashGeneratorInterface $passwordHashGenerator
     */
    public function __construct(PasswordHashGeneratorInterface $passwordHashGenerator)
    {
        $this->passwordHashGenerator = $passwordHashGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function hash($password)
    {
        return $this->passwordHashGenerator->hash($password);
    }

    /**
     * {@inheritdoc}
     */
    public function verify($password, $hash)
    {
        return $this->passwordHashGenerator->verify($password, $hash);
    }
}
