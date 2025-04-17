<?php

namespace ChameleonSystem\SecurityBundle\Controller;

use ChameleonSystem\SecurityBundle\Service\TwoFactorService;
use Scheb\TwoFactorBundle\Model\Google\TwoFactorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class CmsLoginController extends AbstractController
{
    public const DEFAULT_PATH = '/cms';
    public const LOGIN_PATH = '/cms/login';
    public const FIREWALL_BACKEND_COOKIE_NAME = '_security.backend.target_path'; // "backend" is the firewall name
    public const LAST_USED_URL_COOKIE_NAME = '_lastUsedUrl';
    public const LOGIN_REDIRECT_COOKIE_NAME = '_redirectUrl';

    public function __construct(
        private readonly AuthenticationUtils $authenticationUtils,
        readonly RouterInterface $router,
        private readonly RequestStack $requestStack,
        private readonly TwoFactorService $twoFactorService,
        readonly bool $enableGoogleLogin = false,
    ) {
    }

    #[Route('/cms/login', name: 'cms_login')]
    public function index(): Response
    {
        $googleLoginUrl = null;
        if ($this->enableGoogleLogin) {
            $googleLoginUrl = $this->router->generate('connect_google_start');
        }

        // get the login error if there is one
        $error = $this->authenticationUtils->getLastAuthenticationError();
        $lastUsername = $this->authenticationUtils->getLastUsername();

        $this->handleRedirecting();

        return $this->render('@ChameleonSystemSecurity/cms/login/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'googleLoginUrl' => $googleLoginUrl,
        ]);
    }

    #[Route('/cms/2fa/setup', name: '2fa_setup')]
    public function setup(Request $request): Response
    {
        $user = $this->getUser();

        if (false === ($user instanceof TwoFactorInterface)) {
            throw new \LogicException('User does not implement required 2FA interface.');
        }

        if (true === $request->isMethod('POST')) {
            $code = $request->get('code');
            $usedSecret = $request->get('secret');

            $userWithSecret = $this->twoFactorService->cloneUserWithTwoFactorSecret($user, $usedSecret);
            if (true === $this->twoFactorService->checkAuthorizationCode($userWithSecret, $code)) {
                $this->twoFactorService->saveCmsUserAuthenticatorSecret($userWithSecret);
                $this->twoFactorService->adjustSessionUser($userWithSecret);
                $this->addFlash('success', 'Two-factor authentication successfully activated.');

                return $this->redirectToRoute('cms_backend'); // Or wherever you'd like
            } else {
                $this->addFlash('error', 'Invalid authentication code. Please try again.');
            }
        } else {
            if ('' !== $user->getGoogleAuthenticatorSecret()) {
                return $this->redirectToRoute('cms_login'); // Adjust target as needed
            }

            $userWithSecret = $this->twoFactorService->cloneUserWithTwoFactorSecret($user);
        }

        return $this->render('@ChameleonSystemSecurity/cms/2fa/setup.html.twig', [
            'qrCode' => $this->twoFactorService->generateQrCodeDataUri($userWithSecret),
            'secret' => $userWithSecret->getGoogleAuthenticatorSecret(),
        ]);
    }

    protected function handleRedirecting(): void
    {
        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            return;
        }

        if (false === $request->hasSession()) {
            return;
        }

        $session = $request->getSession();

        $redirectUrl = $session->get(self::FIREWALL_BACKEND_COOKIE_NAME);
        if (true === $this->isDefaultPath($redirectUrl)) {
            $redirectUrl = null;
        }

        $referer = $request->headers->get('referer');

        if (null !== $referer && false === $this->isDefaultPath($referer)
            && false === $this->isLoginPath(
                $referer
            )) { // logout from subpage
            $session->set(self::LAST_USED_URL_COOKIE_NAME, $referer);
            $session->set(
                self::FIREWALL_BACKEND_COOKIE_NAME,
                $referer
            ); // used by symfony if directly login after logout
            $redirectUrl = $referer; // used if directly login after logout if update available
        } else {
            // use redirect path if defined; for default login, restore last used path if any
            $redirectUrl ??= $session->get(self::LAST_USED_URL_COOKIE_NAME);
        }

        $session->set(self::LOGIN_REDIRECT_COOKIE_NAME, $redirectUrl);
    }

    protected function isDefaultPath(?string $url): bool
    {
        return null !== $url && self::DEFAULT_PATH === parse_url($url, PHP_URL_PATH)
            && null === parse_url(
                $url,
                PHP_URL_QUERY
            );
    }

    protected function isLoginPath(?string $url): bool
    {
        return null !== $url && self::LOGIN_PATH === parse_url($url, PHP_URL_PATH)
            && null === parse_url(
                $url,
                PHP_URL_QUERY
            );
    }
}
