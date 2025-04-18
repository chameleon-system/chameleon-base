<?php

namespace ChameleonSystem\SecurityBundle\Voter;

use ChameleonSystem\SecurityBundle\CmsUser\CmsUserModel;
use Doctrine\DBAL\Connection;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @template-extends Voter<string,mixed>
 */
class UserHasRightVoter extends Voter
{

    protected function supports(string $attribute, $subject): bool
    {
        return str_starts_with($attribute, 'CMS_RIGHT_');
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var CmsUserModel|UserInterface|null $user */
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