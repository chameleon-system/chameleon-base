<?php

namespace ChameleonSystem\SecurityBundle\Voter;

use ChameleonSystem\SecurityBundle\CmsUser\CmsUserModel;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Permit required access if the subject implements RestrictedByCmsRightsInterface and has at least one matching right for the active user.
 *
 * @template-extends Voter<string,RestrictedByCmsRightsInterface>
 */
class CmsRightVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        if (null === $subject) {
            return false;
        }
        if (false === is_object($subject)) {
            return false;
        }

        if (false === ($subject instanceof RestrictedByCmsRightsInterface)) {
            return false;
        }

        return true;
    }

    /**
     * @param RestrictedByCmsRightsInterface $subject
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $requiredRights = $subject->getPermittedRights($attribute);

        if (0 === \count($requiredRights)) {
            return true;
        }

        /** @var CmsUserModel|UserInterface $user */
        $user = $token->getUser();
        if (false === $user instanceof CmsUserModel) {
            return false;
        }
        $rights = $user->getRights();

        $intersect = array_intersect(array_keys($rights), $requiredRights);

        return count($intersect) > 0;
    }
}
