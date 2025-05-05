<?php
/*@var $oUserAddresses TdbDataExtranetUserAddressList /*
/*@var $aCallTimeVars array */

$oUser = TdbDataExtranetUser::GetInstance();

$sAddressName = 'aUserAddress';
if (array_key_exists('sAddressName', $aCallTimeVars)) {
    $sAddressName = TGlobal::OutHTML($aCallTimeVars['sAddressName']);
}
$sFullFieldName = "{$sAddressName}[selectedAddressId]";

$iSelected = 'new';
if (array_key_exists('selectedAddressId', $aCallTimeVars)) {
    $iSelected = $aCallTimeVars['selectedAddressId'];
}

if ($oUserAddresses->Length() > 0) {
    $oBillingAddress = $oUser->GetBillingAddress();
    $oShippingAddress = $oUser->GetShippingAddress();
    $sTextBilling = 'Rechnungsadresse';
    $sTextShipping = 'Lieferadresse';
    if ($oUser->ShipToBillingAddress()) {
        $sTextBilling = 'Rechungs/Lieferadresse';
        $sTextShipping = $sTextBilling;
    }

    // since we need to use the render select form method we need to collect the data for the address in an array
    $aAddressData = [];
    $aAddressData['new'] = 'Neue Adresse anlegen';
    $oUserAddresses->GoToStart();
    while ($oAddress = $oUserAddresses->Next()) {
        $sName = $oAddress->GetName();
        if ($oAddress->id == $oBillingAddress->id) {
            $sName .= " ({$sTextBilling})";
        } elseif ($oAddress->id == $oShippingAddress->id) {
            $sName .= " ({$sTextShipping})";
        }
        $aAddressData[$oAddress->id] = $sName;
    }
    $sChangeEvent = 'onchange="SetSelectedAddress(this.options[this.selectedIndex].value,\''.TGlobal::OutHTML($sAddressName).'\')"';

    echo TTemplateTools::SelectField($sFullFieldName, $aAddressData, 200, $iSelected, [$sChangeEvent]); ?>

<?php
}
