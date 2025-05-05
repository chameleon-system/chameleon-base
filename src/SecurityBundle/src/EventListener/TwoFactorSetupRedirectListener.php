<?php

namespace ChameleonSystem\SecurityBundle\EventListener;

use Scheb\TwoFactorBundle\Model\Google\TwoFactorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class TwoFactorSetupRedirectListener
{
    public function __construct(
        private RouterInterface $router,
        private bool $twoFactorEnabled = false
    ) {
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        if (false === $this->twoFactorEnabled) {
            return;
        }
        /** @var UserInterface $user */
        $user = $event->getUser();

        if (
            true === ($user instanceof TwoFactorInterface)
            && false === $user->isGoogleAuthenticatorEnabled()
            && '' === $user->getGoogleAuthenticatorSecret()
        ) {
            $event->setResponse(
                new RedirectResponse(
                    $this->router->generate('2fa_setup')
                )
            );
        }
    }
}
