<?php

namespace ChameleonSystem\CoreBundle\EventListener;

use Symfony\Component\Security\Http\Event\LogoutEvent;

class ListCacheResetListener
{
    public function onLogout(LogoutEvent $event): void
    {
        unset($_SESSION['_listObjCache']);
    }
}
