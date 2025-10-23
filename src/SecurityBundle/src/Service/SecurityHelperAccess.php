<?php

namespace ChameleonSystem\SecurityBundle\Service;

use ChameleonSystem\SecurityBundle\Interfaces\ChameleonCmsUserInterface;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Provides access to the private security helper.
 */
class SecurityHelperAccess
{
    public function __construct(readonly private Security $security)
    {
    }

    public function getSecurity(): Security
    {
        return $this->security;
    }

    public function getUser(): ?ChameleonCmsUserInterface
    {
        return $this->security->getUser();
    }

    public function isGranted(mixed $attributes, mixed $subject = null): bool
    {
        if (null === $this->security->getUser()) {
            return false;
        }

        return $this->security->isGranted($attributes, $subject);
    }
}
