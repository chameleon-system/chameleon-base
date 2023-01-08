<?php

namespace ChameleonSystem\SecurityBundle\CmsGoogleLogin;

use ChameleonSystem\SecurityBundle\CmsUser\CmsUserDataAccess;
use ChameleonSystem\SecurityBundle\CmsUser\CmsUserModel;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class GoogleAuthenticator extends OAuth2Authenticator implements AuthenticationEntrypointInterface
{

    public function __construct(
        private readonly ClientRegistry $clientRegistry,
        private readonly GoogleUserRegistrationServiceInterface $registrationService,
        private readonly array $allowedDomains = [],
    ) {
    }

    public function supports(Request $request): ?bool
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return $request->attributes->get('_route') === 'connect_google_check';
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('google_main');
        $accessToken = $this->fetchAccessToken($client);

        $user = $client->fetchUserFromToken($accessToken);
        $hostedDomain = $user->getHostedDomain();
        if ((null !== $hostedDomain) && false === array_key_exists($hostedDomain, $this->allowedDomains)) {
            throw new AuthenticationException(sprintf('The hosted domain %s is not allowed.', $hostedDomain));
        }
        $email = $user->getEmail();
        $mailDomain = mb_substr($email, mb_strpos($email, '@')+1);
        if ((null !== $mailDomain) && false === array_key_exists($mailDomain, $this->allowedDomains)) {
            throw new AuthenticationException(sprintf('The hosted domain %s is not allowed.', $mailDomain));
        }
        if (null === $hostedDomain && null === $mailDomain) {
            throw new AuthenticationException('No hosted domain or email address found.');
        }


        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function() use ($accessToken, $client) {
                /** @var GoogleUser $googleUser */
                $googleUser = $client->fetchUserFromToken($accessToken);

                if (false === $this->registrationService->exists($googleUser)) {
                    return $this->registrationService->register($googleUser);
                }

                return $this->registrationService->update($googleUser);
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // change "app_homepage" to some route in your app
        $targetUrl = '/cms';

        return new RedirectResponse($targetUrl);

        // or, on success, let the request continue to be handled by the controller
        //return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * Called when authentication is needed, but it's not sent.
     * This redirects to the 'login'.
     */
    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new RedirectResponse(
            '/cms/', // might be the site, where users choose their oauth provider
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }
}