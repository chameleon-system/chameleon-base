<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ExtranetBundle\EventListener;

use ChameleonSystem\CoreBundle\Security\Password\PasswordHashGeneratorInterface;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\ExtranetBundle\objects\ExtranetUserEvent;

class RehashPasswordListener
{
    /**
     * @var InputFilterUtilInterface
     */
    private $inputFilterUtil;
    /**
     * @var PasswordHashGeneratorInterface
     */
    private $passwordHashGenerator;

    public function __construct(InputFilterUtilInterface $inputFilterUtil, PasswordHashGeneratorInterface $passwordHashGenerator)
    {
        $this->inputFilterUtil = $inputFilterUtil;
        $this->passwordHashGenerator = $passwordHashGenerator;
    }

    /**
     * @return void
     */
    public function rehashPassword(ExtranetUserEvent $extranetUserEvent)
    {
        $user = $extranetUserEvent->getUser();
        $plainPassword = $this->inputFilterUtil->getFilteredPostInput('password');
        if (null === $plainPassword) {
            return;
        }

        if (false === $this->passwordHashGenerator->needsRehash($user->fieldPassword)) {
            return;
        }

        $hashedPassword = $this->passwordHashGenerator->hash($plainPassword);

        $user->SaveFieldsFast([
            'password' => $hashedPassword,
        ]);
        $user->fieldPassword = $hashedPassword;
    }
}
