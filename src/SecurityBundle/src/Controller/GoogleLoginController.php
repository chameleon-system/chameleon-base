<?php

namespace ChameleonSystem\SecurityBundle\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GoogleLoginController extends AbstractController
{
    public function __construct(private readonly ClientRegistry $clientRegistry)
    {
    }

    #[Route('/cms/google-login', name: 'connect_google_start')]
    public function connectAction()
    {
        return $this->clientRegistry
            ->getClient('google_main') // key used in config/packages/knpu_oauth2_client.yaml
            ->redirect([
                'openid','https://www.googleapis.com/auth/userinfo.email','https://www.googleapis.com/auth/userinfo.profile'
            ]);
    }
}