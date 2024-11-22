<?php

namespace ChameleonSystem\SecurityBundle\Service;

use ChameleonSystem\SecurityBundle\CmsUser\CmsUserModel;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bundle\SecurityBundle\Security\FirewallConfig;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Provides access to the private security helper
 */
class SecurityHelperAccess
{

    public function __construct(readonly private Security $security, readonly private FirewallMap $firewallMap)
    {
    }

    /**
     * Starting with symfony 6.2 the method will be included in the security class - and can then
     * be changed to call the security helper method directly.
     * @param Request $request
     * @return FirewallConfig|null
     */
    public function getFirewallConfig(Request $request): ?FirewallConfig
    {
        return $this->firewallMap->getFirewallConfig($request);
    }

    public function getSecurity(): Security
    {
        return $this->security;
    }

    public function getUser(): null|UserInterface|CmsUserModel
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