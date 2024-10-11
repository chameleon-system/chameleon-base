<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgExtranetMapper_Address extends AbstractPkgExtranetMapper_Address
{
    /**
     * {@inheritdoc}
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements): void
    {
        parent::GetRequirements($oRequirements);
    }

    /**
     * {@inheritdoc}
     */
    public function Accept(IMapperVisitorRestricted $oVisitor, $bCachingEnabled, IMapperCacheTriggerRestricted $oCacheTriggerManager): void
    {
        parent::Accept($oVisitor, $bCachingEnabled, $oCacheTriggerManager);
        /** @var $oAddress TdbDataExtranetUserAddress */
        $oAddress = $oVisitor->GetSourceObject('oAddressObject');
        if ($oAddress && !empty($oAddress->id) && $bCachingEnabled) {
            $oCacheTriggerManager->addTrigger($oAddress->table, $oAddress->id);
        }
        $oSalutation = $oAddress->GetFieldDataExtranetSalutation();
        if ($oSalutation && $bCachingEnabled) {
            $oCacheTriggerManager->addTrigger($oSalutation->table, $oSalutation->id);
        }
        $sSalutation = '';
        if ($oSalutation) {
            $sSalutation = $oSalutation->GetName();
        }
        $oCountry = $oAddress->GetFieldDataCountry();
        $sCountry = '';
        if ($oCountry) {
            $sCountry = $oCountry->GetName();
            if ($bCachingEnabled) {
                $oCacheTriggerManager->addTrigger($oCountry->table, $oCountry->id);
            }
        }
        $oVisitor->SetMappedValue('sSalutation', $sSalutation);
        $oVisitor->SetMappedValue('sFirstName', $oAddress->fieldFirstname);
        $oVisitor->SetMappedValue('sLastName', $oAddress->fieldLastname);
        $oVisitor->SetMappedValue('sAdditionalInfo', $oAddress->fieldAddressAdditionalInfo);
        $oVisitor->SetMappedValue('sAddressStreet', $oAddress->fieldStreet);
        $oVisitor->SetMappedValue('sAddressStreetNr', $oAddress->fieldStreetnr);
        $oVisitor->SetMappedValue('sAddressZip', $oAddress->fieldPostalcode);
        $oVisitor->SetMappedValue('sAddressCity', $oAddress->fieldCity);
        $oVisitor->SetMappedValue('sAddressCountry', $sCountry);
    }
}
