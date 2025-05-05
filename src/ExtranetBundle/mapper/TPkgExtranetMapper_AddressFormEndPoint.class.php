<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgExtranetMapper_AddressFormEndPoint extends AbstractPkgExtranetMapper_Address
{
    /**
     * {@inheritdoc}
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements): void
    {
        parent::GetRequirements($oRequirements);
        $oRequirements->NeedsSourceObject('sCustomMSGConsumer');
        $oRequirements->NeedsSourceObject('sFieldNamesPrefix');
    }

    /**
     * {@inheritdoc}
     */
    public function Accept(IMapperVisitorRestricted $oVisitor, $bCachingEnabled, IMapperCacheTriggerRestricted $oCacheTriggerManager): void
    {
        parent::Accept($oVisitor, $bCachingEnabled, $oCacheTriggerManager);
        $oAddress = $oVisitor->GetSourceObject('oAddressObject');
        $aFieldList = ['aFieldFirstName' => 'firstname',
                            'aFieldLastName' => 'lastname',
                            'aFieldAdditionalInfo' => 'address_additional_info',
                            'aFieldStreet' => 'street',
                            'aFieldStreetNr' => 'streetnr',
                            'aFieldPLZ' => 'postalcode',
                            'aFieldCity' => 'city',
                            'aFieldTel' => 'telefon',
                            'aFieldFax' => 'fax',
        ];
        $this->SetInputFields($aFieldList, $oVisitor, $oAddress);

        $aRadioFieldList = ['aFieldSalutation' => 'data_extranet_salutation_id'];
        $oSalutationList = TdbDataExtranetSalutationList::GetList();
        $this->SetInputFields($aRadioFieldList, $oVisitor, $oAddress, 'radio', $oSalutationList);

        $aSelectFieldList = ['aFieldCountry' => 'data_country_id'];
        $oCountryList = TdbDataCountryList::GetList();
        $this->SetInputFields($aSelectFieldList, $oVisitor, $oAddress, 'select', $oCountryList);

        $sFieldNamesPrefix = $oVisitor->GetSourceObject('sFieldNamesPrefix');
        $oVisitor->SetMappedValue('sFieldNamesPrefix', $sFieldNamesPrefix);
        $oVisitor->SetMappedValue('sAddressId', $oAddress->id);
    }

    /**
     * set errors and values for given field list.
     *
     * @param array<string, string> $aFieldList (MappedFieldName(name used in template) => RealFieldName (user input field name) )
     * @param IMapperVisitorRestricted $oVisitor
     * @param TdbDataExtranetUserAddress $oAddress
     * @param string $sFieldType
     * @param TCMSRecordList|null $oFieldOptionList
     *
     * @internal param string $sMSGConsumer
     *
     * @return void
     */
    protected function SetInputFields($aFieldList, $oVisitor, $oAddress, $sFieldType = 'text', $oFieldOptionList = null)
    {
        foreach ($aFieldList as $sMappedFieldName => $sRealFieldName) {
            $aField = [];
            switch ($sFieldType) {
                case 'radio':
                case 'select':
                    $aField['sValue'] = isset($oAddress->sqlData[$sRealFieldName]) ? $oAddress->sqlData[$sRealFieldName] : ('');
                    $aField['sError'] = $this->GetMessageForField($sRealFieldName, $oVisitor->GetSourceObject('sCustomMSGConsumer'));
                    $aField['aValueList'] = [];
                    if (!is_null($oFieldOptionList)) {
                        /** @var TCMSRecord $oFieldOption */
                        while ($oFieldOption = $oFieldOptionList->Next()) {
                            $aFieldOption = [];
                            $aFieldOption['sName'] = $oFieldOption->GetName();
                            $aFieldOption['sValue'] = $oFieldOption->id;
                            $aField['aValueList'][] = $aFieldOption;
                        }
                    }
                    break;
                case 'checkbox':
                    $aField['sError'] = $this->GetMessageForField($sRealFieldName, $oVisitor->GetSourceObject('sCustomMSGConsumer'));
                    $aField['sValue'] = '';
                    $aField['bIsChecked'] = false;
                    if (is_array($oAddress->sqlData) && isset($oAddress->sqlData[$sRealFieldName]) && !empty($oAddress->sqlData[$sRealFieldName])) {
                        $aField['sValue'] = $oAddress->sqlData[$sRealFieldName];
                        $aField['bIsChecked'] = true;
                    }
                    break;
                default:
                case 'text':
                    $aField['sError'] = $this->GetMessageForField($sRealFieldName, $oVisitor->GetSourceObject('sCustomMSGConsumer'));
                    $aField['sValue'] = '';
                    if (is_array($oAddress->sqlData) && isset($oAddress->sqlData[$sRealFieldName])) {
                        $aField['sValue'] = $oAddress->sqlData[$sRealFieldName];
                    }
                    break;
            }
            $oVisitor->SetMappedValue($sMappedFieldName, $aField);
        }
    }

    /**
     * Set error message for given field from message manager.
     *
     * @param string $sFieldName
     * @param string $sCustomMSGConsumer
     *
     * @return string
     */
    protected function GetMessageForField($sFieldName, $sCustomMSGConsumer)
    {
        $sMessage = '';
        $oMsgManager = TCMSMessageManager::GetInstance();
        if ($oMsgManager->ConsumerHasMessages($sCustomMSGConsumer.'-'.$sFieldName)) {
            $sMessage = $oMsgManager->RenderMessages($sCustomMSGConsumer.'-'.$sFieldName);
        }

        return $sMessage;
    }
}
