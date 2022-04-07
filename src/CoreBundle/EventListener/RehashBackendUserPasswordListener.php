<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\EventListener;

use ChameleonSystem\CoreBundle\Event\BackendLoginEvent;
use ChameleonSystem\CoreBundle\Security\Password\PasswordHashGeneratorInterface;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use Doctrine\DBAL\Connection;
use TCMSUser;
use TGlobal;

/**
 * RehashBackendUserPasswordListener checks if the backend user's password needs to be re-hashed because and older
 * hashing algorithm was still in use when the password was hashed. This listener can only be applied after a successful
 * login, because only then will the plaintext password be available.
 */
class RehashBackendUserPasswordListener
{
    /**
     * @var InputFilterUtilInterface
     */
    private $inputFilterUtil;
    /**
     * @var PasswordHashGeneratorInterface
     */
    private $passwordHashGenerator;
    /**
     * @var Connection
     */
    private $databaseConnection;
    /**
     * @var TGlobal
     */
    private $global;

    /**
     * @param InputFilterUtilInterface       $inputFilterUtil
     * @param PasswordHashGeneratorInterface $passwordHashGenerator
     * @param Connection                     $databaseConnection
     * @param TGlobal                        $global
     */
    public function __construct(InputFilterUtilInterface $inputFilterUtil, PasswordHashGeneratorInterface $passwordHashGenerator, Connection $databaseConnection, TGlobal $global)
    {
        $this->inputFilterUtil = $inputFilterUtil;
        $this->passwordHashGenerator = $passwordHashGenerator;
        $this->databaseConnection = $databaseConnection;
        $this->global = $global;
    }

    /**
     * @param BackendLoginEvent $backendLoginEvent
     */
    public function rehashPassword(BackendLoginEvent $backendLoginEvent)
    {
        $user = $backendLoginEvent->getUser();
        $existingHashedPassword = $user->sqlData['crypted_pw']; // We need to access sqlData because the user object is
        // an instance of TCMSUser, not TAdbCmsUser.

        if (false === $this->passwordHashGenerator->needsRehash($existingHashedPassword)) {
            return;
        }

        $plainPassword = $this->getPlainPassword($user);
        if (null === $plainPassword) {
            return;
        }

        $hashedPassword = $this->passwordHashGenerator->hash($plainPassword);
        $this->saveHashedPassword($user->id, $hashedPassword);
        $user->sqlData['crypted_pw'] = $hashedPassword;
    }

    /**
     * Returns the plain password of the current user. We either expect this password to be submitted by the user
     * (as this listener is called during the login request), returning null if no password was submitted (this
     * should never be the case, but technically it is possible).
     * Or we logged in using the dummy user "www", which happens if the current TCMSUser instance is retrieved from
     * TGlobal::__get() in the frontend. In this case we use the corresponding dummy password retrieved from
     * TGlobal::GetWebuserLoginData(). This allows to rehash the dummy password like any real password if hashing
     * algorithm settings are altered.
     *
     * @param TCMSUser $user
     *
     * @return string|null
     */
    private function getPlainPassword(TCMSUser $user)
    {
        $dummyUserData = $this->global->GetWebuserLoginData();
        $isDummyUser = $user->sqlData['login'] === $dummyUserData['loginName'];

        if (true === $isDummyUser) {
            $plainPassword = $dummyUserData['password'];
        } else {
            /** @var string|null $plainPassword */
            $plainPassword = $this->inputFilterUtil->getFilteredPostInput('password');
        }

        return $plainPassword;
    }

    /**
     * @param string $userId
     * @param string $hashedPassword
     */
    private function saveHashedPassword($userId, $hashedPassword)
    {
        $this->databaseConnection->update('cms_user',
            array(
               'crypted_pw' => $hashedPassword,
            ),
            array(
                'id' => $userId,
            )
        );
    }
}
