<?php

namespace ChameleonSystem\ExtranetBundle\EventListener;

use ChameleonSystem\CoreBundle\Security\AuthenticityToken\AuthenticityTokenManagerInterface;

class RefreshAuthenticityTokenListener
{
    /**
     * @var AuthenticityTokenManagerInterface
     */
    private $authenticityTokenManager;

    public function __construct(AuthenticityTokenManagerInterface $authenticityTokenManager)
    {
        $this->authenticityTokenManager = $authenticityTokenManager;
    }

    /**
     * @return void
     */
    public function refreshAuthenticityToken()
    {
        $this->authenticityTokenManager->refreshToken();
    }
}
