<?php

if (!empty($oUserAddress->fieldCompany)) {
    echo TGlobal::OutHTML($oUserAddress->fieldCompany).'<br />';
}
$oSal = $oUserAddress->GetFieldDataExtranetSalutation();
$aName = [];
if ($oSal) {
    $aName[] = $oSal->fieldName;
}
if (!empty($oUserAddress->fieldFirstname)) {
    $aName[] = $oUserAddress->fieldFirstname;
}
if (!empty($oUserAddress->fieldLastname)) {
    $aName[] = $oUserAddress->fieldLastname;
}
$sName = implode(' ', $aName);
if (!empty($oUserAddress->fieldLastname) || !empty($oUserAddress->fieldFirstname)) {
    if (!empty($oUserAddress->fieldCompany)) {
        echo 'z.Hd. ';
    }
    echo TGlobal::OutHTML($sName).'<br />';
}
if (!empty($oUserAddress->fieldAddressAdditionalInfo)) {
    echo TGlobal::OutHTML($oUserAddress->fieldAddressAdditionalInfo).'<br />';
}
$aParts = [];
if (!empty($oUserAddress->fieldStreet)) {
    $aParts[] = $oUserAddress->fieldStreet;
}
if (!empty($oUserAddress->fieldStreetnr)) {
    $aParts[] = $oUserAddress->fieldStreetnr;
}
$sStreet = implode(' ', $aParts);
if (!empty($sStreet)) {
    echo TGlobal::OutHTML($sStreet).'<br />';
}

$aParts = [];
if (!empty($oUserAddress->fieldPostalcode)) {
    $aParts[] = $oUserAddress->fieldPostalcode;
}
if (!empty($oUserAddress->fieldCity)) {
    $aParts[] = $oUserAddress->fieldCity;
}
$sPart = implode(' ', $aParts);
if (!empty($sPart)) {
    echo TGlobal::OutHTML($sPart).'<br />';
}

$oCountry = $oUserAddress->GetFieldDataCountry();
if ($oCountry) {
    echo TGlobal::OutHTML($oCountry->fieldName);
}
