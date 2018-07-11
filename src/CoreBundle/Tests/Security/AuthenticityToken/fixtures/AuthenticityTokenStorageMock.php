<?php

namespace ChameleonSystem\CoreBundle\Tests\Security\AuthenticityToken\fixtures;

use Symfony\Component\Security\Core\Exception\TokenNotFoundException;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

class AuthenticityTokenStorageMock implements TokenStorageInterface
{
    /**
     * @var array
     */
    private $tokenList = [];

    /**
     * {@inheritdoc}
     */
    public function getToken($tokenId)
    {
        if (false === $this->hasToken($tokenId)) {
            throw new TokenNotFoundException('Token not found.');
        }

        return $this->tokenList[$tokenId];
    }

    /**
     * {@inheritdoc}
     */
    public function setToken($tokenId, $token)
    {
        $this->tokenList[$tokenId] = $token;
    }

    /**
     * {@inheritdoc}
     */
    public function removeToken($tokenId)
    {
        unset($this->tokenList[$tokenId]);
    }

    /**
     * {@inheritdoc}
     */
    public function hasToken($tokenId)
    {
        return isset($this->tokenList[$tokenId]);
    }
}
