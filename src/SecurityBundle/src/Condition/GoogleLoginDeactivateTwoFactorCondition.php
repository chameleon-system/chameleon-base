<?php

namespace ChameleonSystem\SecurityBundle\Condition;

use ChameleonSystem\SecurityBundle\ChameleonSystemSecurityConstants;
use Scheb\TwoFactorBundle\Security\TwoFactor\AuthenticationContextInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Condition\TwoFactorConditionInterface;

class GoogleLoginDeactivateTwoFactorCondition implements TwoFactorConditionInterface
{
    public function shouldPerformTwoFactorAuthentication(AuthenticationContextInterface $context): bool
    {
        if (ChameleonSystemSecurityConstants::GOOGLE_RETURN_ROUTE_NAME === $context->getRequest()->attributes->get('_route', '')) {
            return false;
        }

        return true;
    }
}
