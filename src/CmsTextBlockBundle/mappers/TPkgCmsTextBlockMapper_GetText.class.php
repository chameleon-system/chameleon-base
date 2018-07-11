<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgCmsTextBlockMapper_GetText extends AbstractViewMapper
{
    /**
     * {@inheritdoc}
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements)
    {
        $oRequirements->NeedsSourceObject('name', 'string'); // the system name of the text-object to load
        $oRequirements->NeedsSourceObject('maxwidth', 'int', 600); // the system name of the text-object to load
    }

    /**
     * {@inheritdoc}
     */
    public function Accept(IMapperVisitorRestricted $oVisitor, $bCachingEnabled, IMapperCacheTriggerRestricted $oCacheTriggerManager)
    {
        $sSystemName = $oVisitor->GetSourceObject('name');
        $oBlock = TdbPkgCmsTextBlock::GetInstanceFromSystemName($sSystemName);
        if ($oBlock) {
            if ($bCachingEnabled) {
                $oCacheTriggerManager->addTrigger($oBlock->table, $oBlock->id);
            }
            $oVisitor->SetMappedValue('title', $oBlock->fieldName);
            $oVisitor->SetMappedValue('text', $oBlock->GetTextField('content', $oVisitor->GetSourceObject('maxwidth')));
        }
    }
}
