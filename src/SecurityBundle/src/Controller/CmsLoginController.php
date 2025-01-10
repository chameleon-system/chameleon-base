<?php

namespace ChameleonSystem\SecurityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class CmsLoginController extends AbstractController
{
    public function __construct(
        private readonly AuthenticationUtils $authenticationUtils,
        readonly RouterInterface $router,
        private readonly RequestStack $requestStack,
        readonly bool $enableGoogleLogin = false,
    ) {
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

        $request = $this->requestStack->getCurrentRequest();
        if (true === $request->hasSession()) {
            $session = $request->getSession();
            $targetPath = $session->get('_security.backend.target_path'); // "backend" is the firewall name
            $session->set('_targetPath', $targetPath);
        }

        return $this->render('@ChameleonSystemSecurity/cms/login/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'googleLoginUrl' => $googleLoginUrl,
        ]);
    }
}
