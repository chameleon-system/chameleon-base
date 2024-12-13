<?php

namespace ChameleonSystem\SecurityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class CmsLoginController extends AbstractController
{
    public function __construct(readonly private AuthenticationUtils $authenticationUtils, readonly RouterInterface $router, readonly bool $enableGoogleLogin = false)
    {
    }

    #[\Symfony\Component\Routing\Attribute\Route('/cms/login', name: 'cms_login')]
    public function index(): Response
    {
        $googleLoginUrl = null;
        if ($this->enableGoogleLogin) {
            $googleLoginUrl = $this->router->generate('connect_google_start');
        }

        // get the login error if there is one
        $error = $this->authenticationUtils->getLastAuthenticationError();
        $lastUsername = $this->authenticationUtils->getLastUsername();

        return $this->render('@ChameleonSystemSecurity/cms/login/index.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
            'googleLoginUrl' => $googleLoginUrl
        ]);
    }
}