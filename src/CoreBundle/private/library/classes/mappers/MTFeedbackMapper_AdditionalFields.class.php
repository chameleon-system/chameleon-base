<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class MTFeedbackMapper_AdditionalFields extends MTFeedbackMapper_StandardForm
{
    /**
     * {@inheritdoc}
     */
    public function Accept(
        IMapperVisitorRestricted $oVisitor,
        $bCachingEnabled,
        IMapperCacheTriggerRestricted $oCacheTriggerManager
    ): void {
        parent::Accept($oVisitor, $bCachingEnabled, $oCacheTriggerManager);

        /** @var $oFeedbackErrorList MTFeedbackErrors */
        $oFeedbackErrorList = $oVisitor->GetSourceObject('oFeedbackErrorList');
        $aFieldInput = $oVisitor->GetSourceObject('aFieldInput');

        $aSelectFieldList = ['aFieldCountry' => 'data_country_id'];
        $oCountryList = TdbDataCountryList::GetList();

        $aSelectFieldList = $this->SetInputFields(
            $aSelectFieldList,
            $oFeedbackErrorList,
            $aFieldInput,
            'select',
            $oCountryList
        );
        $oVisitor->SetMappedValueFromArray($aSelectFieldList);
    }

    /**
     * {@inheritdoc}
     */
    protected function getFieldList()
    {
        $fields = parent::getFieldList();

        $additionalFields = [
            'aFieldFirstName' => 'firstname',
            'aFieldAdditionalInfo' => 'address_additional_info',
            'aFieldStreet' => 'street',
            'aFieldPLZ' => 'postalcode',
            'aFieldCity' => 'city',
            'aFieldTel' => 'tel',
        ];

        return array_merge($fields, $additionalFields);
    }
}
