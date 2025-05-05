<?php

namespace ChameleonSystem\SecurityBundle\Bridge\Chameleon\TableObject;

use ChameleonSystem\SecurityBundle\Voter\RestrictedByCmsGroupInterface;

class CmsFieldConf extends \ChameleonSystemSecurityBundleBridgeChameleonTableObjectCmsFieldConfAutoParent implements RestrictedByCmsGroupInterface
{
    public function getPermittedGroups(?string $qualifier = null): array
    {
        return $this->GetPermissionGroups();
    }
}
