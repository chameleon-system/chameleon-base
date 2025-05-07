<?php

namespace ChameleonSystem\SecurityBundle\CmsGoogleLogin;

use ChameleonSystem\SecurityBundle\Badge\UsedAuthenticatorBadge;
use ChameleonSystem\SecurityBundle\ChameleonSystemSecurityConstants;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class GoogleAuthenticator extends OAuth2Authenticator implements AuthenticationEntryPointInterface
{
    /**
     * @param string[] $allowedDomains
     */
    public function __construct(
        private readonly ClientRegistry $clientRegistry,
        private readonly GoogleUserRegistrationServiceInterface $registrationService,
        private readonly array $allowedDomains = [],
    ) {
    }

    public function supports(Request $request): ?bool
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return ChameleonSystemSecurityConstants::GOOGLE_RETURN_ROUTE_NAME === $request->attributes->get('_route');
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('google_main');
        $accessToken = $this->fetchAccessToken($client);

        $user = $client->fetchUserFromToken($accessToken);
        assert($user instanceof GoogleUser);

        $hostedDomain = $user->getHostedDomain();
        if ((null !== $hostedDomain) && false === in_array($hostedDomain, $this->allowedDomains, true)) {
            throw new AuthenticationException(sprintf('The hosted domain %s is not allowed.', $hostedDomain));
        }
        if (null === $hostedDomain) {
            throw new AuthenticationException('No hosted domain found.');
        }

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function () use ($accessToken, $client) {
                /** @var GoogleUser $googleUser */
                $googleUser = $client->fetchUserFromToken($accessToken);

                if (false === $this->registrationService->exists($googleUser)) {
                    return $this->registrationService->register($googleUser);
                }

                return $this->registrationService->update($googleUser);
            }),
            [new UsedAuthenticatorBadge(GoogleAuthenticator::class)]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse(PATH_CMS_CONTROLLER);
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
    public function start(Request $request, ?AuthenticationException $authException = null): Response
    {
        return new RedirectResponse(
            PATH_CMS_CONTROLLER,
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }
}
