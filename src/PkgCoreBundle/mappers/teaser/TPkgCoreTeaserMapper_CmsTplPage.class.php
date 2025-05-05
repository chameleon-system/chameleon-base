<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgCoreTeaserMapper_CmsTplPage extends AbstractViewMapper
{
    /**
     * {@inheritDoc}
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements): void
    {
        $oRequirements->NeedsSourceObject('oObject', 'TdbCmsTplPage');
    }

    /**
     * {@inheritDoc}
     */
    public function Accept(IMapperVisitorRestricted $oVisitor, $bCachingEnabled, IMapperCacheTriggerRestricted $oCacheTriggerManager): void
    {
        /** @var TdbCmsTplPage $oPage */
        $oPage = $oVisitor->GetSourceObject('oObject');
        if ($oPage && $bCachingEnabled) {
            $oCacheTriggerManager->addTrigger($oPage->table, $oPage->id);
        }

        $oPageImage = $oPage->GetImage(0, 'images', true);

        if ($oPageImage && $bCachingEnabled) {
            $oCacheTriggerManager->addTrigger('cms_media', $oPageImage->id);
        }
        $aData = [];
        $aData['sImageUrl'] = $oPageImage->GetRelativeURL();
        $aData['sHeadline'] = $oPage->fieldName;
        $aData['sLink'] = $oPage->GetRealURL();
        $aData['sTeaserText'] = $oPage->fieldMetaDescription;

        $oVisitor->SetMappedValueFromArray($aData);
    }
}
