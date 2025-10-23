<?php

namespace ChameleonSystem\SecurityBundle\Voter;

use ChameleonSystem\SecurityBundle\CmsUser\CmsUserModel;
use ChameleonSystem\SecurityBundle\Interfaces\ChameleonCmsUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @template-extends Voter<string,mixed>
 */
class UserHasRightVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        return str_starts_with($attribute, CmsVoterPrefixConstants::RIGHT);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var ChameleonCmsUserInterface|null $user */
        $user = $token->getUser();
        if (null === $user) {
            return false;
        }

        if (false === ($user instanceof CmsUserModel)) {
            return false;
        }

        $rights = $user->getRights();

        return in_array($attribute, $rights, true);
    }
}
