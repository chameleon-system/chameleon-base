<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

abstract class TPkgViewRendererAbstractModuleMapper extends AbstractViewMapper
{
    /**
     * {@inheritdoc}
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements): void
    {
        $oRequirements->NeedsSourceObject('instanceID', 'string', null);
        $oRequirements->NeedsSourceObject('aModuleConfig', 'array');
        $oRequirements->NeedsSourceObject('sModuleSpotName', 'string');
    }
}
