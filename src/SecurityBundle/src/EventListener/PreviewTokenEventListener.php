<?php

namespace ChameleonSystem\SecurityBundle\EventListener;

use ChameleonSystem\CoreBundle\Service\PreviewModeServiceInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;

readonly class PreviewTokenEventListener
{
    public function __construct(private PreviewModeServiceInterface $previewModeService)
    {
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();

        $this->previewModeService->grantPreviewAccess(true, $user->getId());
    }

    public function onLogout(LogoutEvent $event): void
    {
        $user = $event->getToken()?->getUser();

        if (null === $user || false === method_exists($user, 'getId')) {
            return;
        }

        $this->previewModeService->grantPreviewAccess(false, $user->getId());
    }
}
