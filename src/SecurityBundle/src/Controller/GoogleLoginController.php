<?php

namespace ChameleonSystem\SecurityBundle\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GoogleLoginController extends AbstractController
{
    private const GOOGLE_SCOPES = [
        'openid',
        'https://www.googleapis.com/auth/userinfo.email',
        'https://www.googleapis.com/auth/userinfo.profile',
    ];

    public function __construct(private readonly ClientRegistry $clientRegistry)
    {
    }

    #[Route('/cms/google-login', name: 'connect_google_start')]
    public function connectAction(): Response
    {
        return $this->clientRegistry
            ->getClient('google_main') // key used in config/packages/knpu_oauth2_client.yaml
            ->redirect(self::GOOGLE_SCOPES, []);
    }

    /**
     * After going to google, you're redirected back here
     * note however, that the method itself will not be called. Instead, `\ChameleonSystem\SecurityBundle\CmsGoogleLogin\GoogleAuthenticator::authenticate` will handle the request.
     */
    #[Route(path: '/cms/google-check', name: 'connect_google_check')]
    public function connectCheckAction(Request $request, ClientRegistry $clientRegistry): Response
    {
        return new Response('nothing to do');
        // since we want to authenticate the user in symfony, we use a guard authenticator
        // \ChameleonSystem\SecurityBundle\CmsGoogleLogin\GoogleAuthenticator::authenticate
        // and do nothing in this method.
    }
}
