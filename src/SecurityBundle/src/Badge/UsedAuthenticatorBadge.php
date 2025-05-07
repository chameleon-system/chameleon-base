<?php

namespace ChameleonSystem\SecurityBundle\Badge;

use Symfony\Component\Security\Http\Authenticator\Passport\Badge\BadgeInterface;

class UsedAuthenticatorBadge implements BadgeInterface
{
    public function __construct(private readonly string $name)
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isResolved(): bool
    {
        return true;
    }
}
