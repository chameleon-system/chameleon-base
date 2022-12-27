<?php

namespace ChameleonSystem\SecurityBundle\Service;

use ChameleonSystem\SecurityBundle\CmsUser\CmsUserModel;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Provides access to the private security helper
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

    public function getUser(): null|UserInterface|CmsUserModel
    {
        return $this->security->getUser();
    }

    public function isGranted($attributes, $subject = null): bool
    {
        if (null === $this->security->getUser()) {
            return false;
        }
        return $this->security->isGranted($attributes, $subject);
    }
}