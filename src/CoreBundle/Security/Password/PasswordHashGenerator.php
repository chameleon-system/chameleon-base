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

class PasswordHashGenerator implements PasswordHashGeneratorInterface
{
    /**
     * @var HashAlgorithmInterface
     */
    private $hashAlgorithm;

    public function __construct(HashAlgorithmInterface $hashAlgorithm)
    {
        $this->hashAlgorithm = $hashAlgorithm;
    }

    /**
     * {@inheritdoc}
     */
    public function hash($plainPassword)
    {
        return $this->hashAlgorithm->hash($plainPassword);
    }

    /**
     * {@inheritdoc}
     */
    public function verify($plainPassword, $hash)
    {
        if (\mb_strlen($plainPassword) > self::MAXIMUM_PASSWORD_LENGTH) {
            return false;
        }

        return $this->hashAlgorithm->verify($plainPassword, $hash);
    }

    /**
     * {@inheritdoc}
     */
    public function needsRehash($hash)
    {
        return false === $this->hashAlgorithm->isHashedWithCurrentSettings($hash);
    }
}
