<?php

namespace ChameleonSystem\CoreBundle\Bridge\Chameleon\TableObject;

class CmsPortalTableObject extends \ChameleonSystemCoreBundleBridgeChameleonTableObjectCmsPortalTableObjectAutoParent
{
    private array $runtimeCache = [];
    private const string DOMAIN_LIST_CACHE_KEY = 'DOMAIN_LIST_CACHE_KEY';
    public function GetFieldCmsPortalDomainsList()
    {
        if (isset($this->runtimeCache[self::DOMAIN_LIST_CACHE_KEY])) {
            return $this->runtimeCache[self::DOMAIN_LIST_CACHE_KEY];
        }
        $this->runtimeCache[self::DOMAIN_LIST_CACHE_KEY] = parent::GetFieldCmsPortalDomainsList();

        return $this->runtimeCache[self::DOMAIN_LIST_CACHE_KEY];
    }

}