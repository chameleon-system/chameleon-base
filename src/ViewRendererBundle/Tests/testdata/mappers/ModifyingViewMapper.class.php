<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class ModifyingViewMapper extends AbstractViewMapper
{
    /**
     * {@inheritdoc}
     */
    public function Accept(IMapperVisitorRestricted $oVisitor, $bCachingEnabled, IMapperCacheTriggerRestricted $oCacheTriggerManager): void
    {
        $iNeedThis = $oVisitor->GetSourceObject('title');
        if (null === $iNeedThis) {
            throw new MapperException('i need a title');
        }
        // i do stuff with $iNeedThis
        $oVisitor->SetMappedValue('title', $iNeedThis.'_modified');
    }

    /**
     * {@inheritdoc}
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements): void
    {
        $oRequirements->NeedsSourceObject('title');
    }
}
