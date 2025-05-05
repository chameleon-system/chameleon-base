<?php

namespace ChameleonSystem\SecurityBundle\Bridge\Chameleon\TableObject;

use ChameleonSystem\SecurityBundle\Voter\RestrictedByCmsRightsInterface;

class CmsMenuCustomItem extends \ChameleonSystemSecurityBundleBridgeChameleonTableObjectCmsMenuCustomItemAutoParent implements RestrictedByCmsRightsInterface
{
    public function getPermittedRights(?string $qualifier = null): array
    {
        return $this->GetFieldCmsRightIdList();
    }
}
