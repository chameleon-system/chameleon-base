<?php

namespace ChameleonSystem\SecurityBundle\Voter;

use ChameleonSystem\SecurityBundle\CmsUser\CmsUserModel;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @template-extends Voter<string,RestrictedByCmsGroupInterface>
 */
class CmsGroupVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        if (null === $subject) {
            return false;
        }
        if (false === is_object($subject)) {
            return false;
        }

        if (false === ($subject instanceof RestrictedByCmsGroupInterface)) {
            return false;
        }

        return true;
    }

    /**
     * @param RestrictedByCmsGroupInterface $subject
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $permittedGroups = $subject->getPermittedGroups($attribute);

        if (0 === \count($permittedGroups)) {
            return true;
        }

        /** @var CmsUserModel|UserInterface $user */
        $user = $token->getUser();
        if (false === $user instanceof CmsUserModel) {
            return false;
        }
        $groups = $user->getGroups();

        $intersect = array_intersect(array_keys($groups), $permittedGroups);

        return count($intersect) > 0;
    }
}
