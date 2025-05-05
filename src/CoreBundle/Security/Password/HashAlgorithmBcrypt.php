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
 * @deprecated 7.2 - symfony security layer used
 */
class HashAlgorithmBcrypt implements HashAlgorithmInterface
{
    /**
     * @var int
     */
    private $cost;

    /**
     * @param int $cost
     */
    public function __construct($cost = 10)
    {
        $this->cost = $cost;
    }

    /**
     * {@inheritdoc}
     */
    public function hash($plainPassword)
    {
        return password_hash($plainPassword, PASSWORD_BCRYPT, [
            'cost' => $this->cost,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function verify($plainPassword, $hash)
    {
        return password_verify($plainPassword, $hash);
    }

    /**
     * {@inheritdoc}
     */
    public function isHashedByThisAlgorithm($hash)
    {
        return 0 === strpos($hash, '$2y$');
    }

    /**
     * {@inheritdoc}
     */
    public function isHashedWithCurrentSettings($hash)
    {
        return 0 === strpos($hash, '$2y$'.$this->cost.'$');
    }
}
