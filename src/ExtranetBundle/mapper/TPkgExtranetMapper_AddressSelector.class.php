<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\ActivePageServiceInterface;

class TPkgExtranetMapper_AddressSelector extends AbstractPkgExtranetMapper_Address
{
    /**
     * {@inheritdoc}
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements): void
    {
        parent::GetRequirements($oRequirements);
        $oRequirements->NeedsSourceObject('bAddressTypeIsBilling');
        $oRequirements->NeedsSourceObject('sSpotName');
        $oRequirements->NeedsSourceObject('oActivePage', 'TCMSActivePage', $this->getActivePageService()->getActivePage());
    }

    /**
     * {@inheritdoc}
     */
    public function Accept(IMapperVisitorRestricted $oVisitor, $bCachingEnabled, IMapperCacheTriggerRestricted $oCacheTriggerManager): void
    {
        parent::Accept($oVisitor, $bCachingEnabled, $oCacheTriggerManager);
        /** @var TdbDataExtranetUserAddress $oAddress */
        $oAddress = $oVisitor->GetSourceObject('oAddressObject');
        $bAddressTypeIsBilling = $oVisitor->GetSourceObject('bAddressTypeIsBilling');

        $isActiveShippingAddress = false;
        $isActiveBillingAddress = false;
        $oUserForAddress = $oAddress->GetFieldDataExtranetUser();
        if ($oUserForAddress) {
            $isActiveBillingAddress = ($oUserForAddress->fieldDefaultBillingAddressId === $oAddress->id);
            $isActiveShippingAddress = ($oUserForAddress->fieldDefaultShippingAddressId === $oAddress->id);
            $oVisitor->SetMappedValueFromArray(
                [
                    'isActiveBillingAddress' => $isActiveBillingAddress,
                    'isActiveShippingAddress' => $isActiveShippingAddress,
                ]
            );
        }

        if ($bAddressTypeIsBilling) {
            $oVisitor->SetMappedValue('sExtranetHandlerSelectFunction', 'SelectBillingAddress');
            $oVisitor->SetMappedValue('bAllowDelete', !$isActiveBillingAddress);
            $oVisitor->SetMappedValue('sExtranetHandlerDeleteFunction', 'DeleteBillingAddress');
            $oVisitor->SetMappedValue('sFieldNamesPrefix', TdbDataExtranetUserAddress::FORM_DATA_NAME_BILLING);
            $oVisitor->SetMappedValue('allowSelect', !$isActiveBillingAddress);
        } else {
            $oVisitor->SetMappedValue('sExtranetHandlerSelectFunction', 'SelectShippingAddress');

            $bAllowDelete = true;
            $oUserForAddress = $oAddress->GetFieldDataExtranetUser();
            if ($oUserForAddress) {
                $oBillingAddress = $oUserForAddress->GetBillingAddress();
                if ($oBillingAddress && $oBillingAddress->id == $oAddress->id) {
                    $bAllowDelete = false;
                }
            }
            $oVisitor->SetMappedValue('sExtranetHandlerDeleteFunction', 'DeleteShippingAddress');
            $oVisitor->SetMappedValue('bAllowDelete', $bAllowDelete);
            $oVisitor->SetMappedValue('sFieldNamesPrefix', TdbDataExtranetUserAddress::FORM_DATA_NAME_SHIPPING);
        }
        $oExtranetConfig = TdbDataExtranet::GetInstance();
        $oVisitor->SetMappedValue('sExtranetSpot', $oExtranetConfig->fieldExtranetSpotName);
        $oVisitor->SetMappedValue('sSpotName', $oVisitor->GetSourceObject('sSpotName'));
        $oVisitor->SetMappedValue('sAddressId', $oAddress->id);

        /** @var TCMSActivePage $oActivePage* */
        $oActivePage = $oVisitor->GetSourceObject('oActivePage');

        $oVisitor->SetMappedValue('sFormUrl', $oActivePage->GetRealURLPlain());
    }

    /**
     * @return ActivePageServiceInterface
     */
    private function getActivePageService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.active_page_service');
    }
}
