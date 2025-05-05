<?php

namespace ChameleonSystem\CoreBundle\Security\AuthenticityToken;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Csrf\Exception\TokenNotFoundException;
use Symfony\Component\Security\Csrf\TokenStorage\ClearableTokenStorageInterface;
use Symfony\Component\Security\Csrf\TokenStorage\SessionTokenStorage;

/**
 * Mimics \Symfony\Component\Security\Csrf\TokenStorage\SessionTokenStorage, but avoids injecting the session service
 * as this doesn't work in Chameleon.
 */
class AuthenticityTokenStorage implements ClearableTokenStorageInterface
{
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var string
     */
    private $namespace;

    public function __construct(RequestStack $requestStack, string $namespace = SessionTokenStorage::SESSION_NAMESPACE)
    {
        $this->requestStack = $requestStack;
        $this->namespace = $namespace;
    }

    public function clear(): void
    {
        $session = $this->getSession();
        if (null === $session) {
            return;
        }
        $sessionKeys = $session->all();
        foreach ($sessionKeys as $key => $value) {
            if (false === str_starts_with($this->namespace.'/', $key)) {
                continue;
            }

            $session->remove($key);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getToken(string $tokenId): string
    {
        $session = $this->getSession();
        if (null === $session || false === $session->has($this->namespace.'/'.$tokenId)) {
            throw new TokenNotFoundException('The CSRF token with ID '.$tokenId.' does not exist.');
        }

        return (string) $session->get($this->namespace.'/'.$tokenId);
    }

    /**
     * @return SessionInterface|null
     */
    private function getSession()
    {
        $request = $this->requestStack->getMainRequest();
        if (null === $request) {
            return null;
        }
        if (false === $request->hasSession()) {
            return null;
        }
        $session = $request->getSession();
        if (false === $session->isStarted()) {
            $session->start();
        }

        return $session;
    }

    /**
     * {@inheritDoc}
     *
     * @param string $tokenId The token ID
     * @param string $token The CSRF token
     */
    public function setToken(string $tokenId, string $token): void
    {
        $session = $this->getSession();
        if (null === $session) {
            return;
        }
        $session->set($this->namespace.'/'.$tokenId, (string) $token);
    }

    /**
     * {@inheritdoc}
     */
    public function removeToken(string $tokenId): ?string
    {
        $session = $this->getSession();
        if (null === $session) {
            return null;
        }

        return $session->remove($this->namespace.'/'.$tokenId);
    }

    /**
     * {@inheritdoc}
     */
    public function hasToken(string $tokenId): bool
    {
        $session = $this->getSession();
        if (null === $session) {
            return false;
        }

        return $session->has($this->namespace.'/'.$tokenId);
    }
}
