<?php

namespace ChameleonSystem\SecurityBundle\Voter;

use ChameleonSystem\SecurityBundle\CmsUser\CmsUserModel;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @template-extends Voter<string,RestrictedByCmsRoleInterface>
 */
class CmsRoleVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        if (null === $subject) {
            return false;
        }
        if (false === is_object($subject)) {
            return false;
        }

        if (false === ($subject instanceof RestrictedByCmsRoleInterface)) {
            return false;
        }

        return true;
    }

    /**
     * @param RestrictedByCmsRoleInterface $subject
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $permittedRoles = $subject->getPermittedRoles($attribute);

        if (0 === \count($permittedRoles)) {
            return true;
        }

        /** @var CmsUserModel|UserInterface $user */
        $user = $token->getUser();
        if (false === $user instanceof CmsUserModel) {
            return false;
        }

        $roles = $user->getRoles();

        $intersect = array_intersect(array_keys($roles), $permittedRoles);

        return count($intersect) > 0;
    }
}
