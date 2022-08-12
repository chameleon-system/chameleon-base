<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgViewRendererModuleLegacyMapper extends TPkgViewRendererAbstractModuleMapper
{
    /**
     * {@inheritdoc}
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements): void
    {
        parent::GetRequirements($oRequirements);
        $oRequirements->NeedsSourceObject('oModuleInstance', 'TModelBase');
        $oRequirements->NeedsSourceObject('_moduleID', 'string');
    }

    /**
     * {@inheritdoc}
     */
    public function Accept(IMapperVisitorRestricted $oVisitor, $bCachingEnabled, IMapperCacheTriggerRestricted $oCacheTriggerManager): void
    {
        /** @var TModelBase $oModuleInstance */
        $oModuleInstance = $oVisitor->GetSourceObject('oModuleInstance');
        $aData = $oModuleInstance->Execute();
        foreach (array_keys($aData) as $sKey) {
            $oVisitor->SetMappedValue($sKey, $aData[$sKey]);
        }

        if (true === $oModuleInstance->_AllowCache() && true === $bCachingEnabled) {
            $aCacheTrigger = $oModuleInstance->_GetCacheTableInfos();
            if (is_array($aCacheTrigger)) {
                foreach ($aCacheTrigger as $aTrigger) {
                    $oCacheTriggerManager->addTrigger($aTrigger['table'], (true === isset($aTrigger['id'])) ? ($aTrigger['id']) : (null));
                }
            }
        }
        $oVisitor->SetMappedValue('_moduleID', $oVisitor->GetSourceObject('_moduleID'));
    }
}
