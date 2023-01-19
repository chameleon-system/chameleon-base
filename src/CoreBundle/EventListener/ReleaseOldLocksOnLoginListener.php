<?php

namespace ChameleonSystem\CoreBundle\EventListener;


use Doctrine\DBAL\Connection;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class ReleaseOldLocksOnLoginListener
{
    public function __construct(readonly private Connection $connection)
    {
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $query = 'DELETE FROM `cms_lock` WHERE TIMESTAMPDIFF(MINUTE,`time_stamp`,CURRENT_TIMESTAMP()) >= :lockTimeout';
        $this->connection->executeQuery($query, ['lockTimeout' => RECORD_LOCK_TIMEOUT], ['lockTimeout' => \PDO::PARAM_INT]);
    }
}