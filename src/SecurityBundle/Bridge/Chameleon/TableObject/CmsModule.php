<?php

namespace ChameleonSystem\SecurityBundle\Bridge\Chameleon\TableObject;

use ChameleonSystem\SecurityBundle\Voter\RestrictedByCmsGroupInterface;

class CmsModule extends \ChameleonSystemSecurityBundleBridgeChameleonTableObjectCmsModuleAutoParent implements RestrictedByCmsGroupInterface
{
    public function getPermittedGroups(?string $qualifier = null): array
    {
        return [$this->fieldCmsUsergroupId];
    }
}
