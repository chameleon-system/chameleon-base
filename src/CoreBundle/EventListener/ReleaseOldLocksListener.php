<?php

namespace ChameleonSystem\CoreBundle\EventListener;

use Doctrine\DBAL\Connection;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;

readonly class ReleaseOldLocksListener
{
    public function __construct(private Connection $connection)
    {
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $query = 'DELETE FROM `cms_lock` WHERE TIMESTAMPDIFF(MINUTE,`time_stamp`,CURRENT_TIMESTAMP()) >= :lockTimeout';
        $this->connection->executeQuery($query, ['lockTimeout' => RECORD_LOCK_TIMEOUT], ['lockTimeout' => \PDO::PARAM_INT]);
    }

    public function onLogout(LogoutEvent $event): void
    {
        $user = $event->getToken()?->getUser();

        if (!$user || !method_exists($user, 'getId')) {
            return;
        }

        $query = 'DELETE FROM `cms_lock` WHERE `cms_user_id` = :cmsUserId';
        $this->connection->executeQuery($query, ['cmsUserId' => $user->getId()], ['cmsUserId' => \PDO::PARAM_STR]);

        $query = 'DELETE FROM `cms_lock` WHERE TIMESTAMPDIFF(MINUTE,`time_stamp`,CURRENT_TIMESTAMP()) >= :lockTimeout';
        $this->connection->executeQuery($query, ['lockTimeout' => RECORD_LOCK_TIMEOUT], ['lockTimeout' => \PDO::PARAM_INT]);
    }
}
