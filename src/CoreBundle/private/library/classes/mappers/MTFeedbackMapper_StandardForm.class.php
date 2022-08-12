<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Contracts\Translation\TranslatorInterface;

class MTFeedbackMapper_StandardForm extends AbstractViewMapper
{
    /**
     * {@inheritdoc}
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements)
    {
        $oRequirements->NeedsSourceObject('oFeedbackModuleConfiguration', 'TdbModuleFeedback');
        $oRequirements->NeedsSourceObject('oFeedbackErrorList', 'MTFeedbackErrorsCore');
        $oRequirements->NeedsSourceObject('aFieldInput', 'array', array());
        $oRequirements->NeedsSourceObject('sSpotName');
    }

    /**
     * {@inheritdoc}
     */
    public function Accept(IMapperVisitorRestricted $oVisitor, $bCachingEnabled, IMapperCacheTriggerRestricted $oCacheTriggerManager)
    {
        $aFieldList = $this->getFieldList();
        /** @var $oFeedbackModuleConfiguration TdbModuleFeedback */
        $oFeedbackModuleConfiguration = $oVisitor->GetSourceObject('oFeedbackModuleConfiguration');
        /** @var $oFeedbackErrorList MTFeedbackErrors */
        $oFeedbackErrorList = $oVisitor->GetSourceObject('oFeedbackErrorList');
        $aFieldInput = $oVisitor->GetSourceObject('aFieldInput');
        $sSpotName = $oVisitor->GetSourceObject('sSpotName');
        $aTextData = array();
        $aTextData['sHeadline'] = $oFeedbackModuleConfiguration->fieldName;
        $aTextData['sText'] = $oFeedbackModuleConfiguration->GetTextField('text');
        $aFieldList = $this->SetInputFields($aFieldList, $oFeedbackErrorList, $aFieldInput);
        $oVisitor->SetMappedValueFromArray($aFieldList);
        $oVisitor->SetMappedValue('aTextData', $aTextData);
        $oVisitor->SetMappedValue('sSpotName', $sSpotName);
    }

    /**
     * @return array
     */
    protected function getFieldList()
    {
        return array(
            'aFieldName' => 'name',
            'aFieldEMail' => 'email',
            'aFieldSubject' => 'subject',
            'aFieldMessage' => 'body',
        );
    }

    /**
     * Init field data with errors and user data.
     *
     * @param array                $aFieldList
     * @param MTFeedbackErrorsCore $oFeedbackErrorList
     * @param array                $aFieldInput
     * @param string               $sFieldType
     * @param TCMSRecordList       $oFieldOptionList
     *
     * @return array
     */
    protected function SetInputFields($aFieldList, $oFeedbackErrorList, $aFieldInput, $sFieldType = 'text', $oFieldOptionList = null)
    {
        foreach ($aFieldList as $sMappedFieldName => $sRealFieldName) {
            $aFieldList[$sMappedFieldName] = array();
            switch ($sFieldType) {
                case 'text':
                    $aFieldList[$sMappedFieldName]['sValue'] = isset($aFieldInput[$sRealFieldName]) ? $aFieldInput[$sRealFieldName] : '';
                    break;
                case 'select':
                    $aFieldList[$sMappedFieldName]['sValue'] = isset($aFieldInput[$sRealFieldName]) ? $aFieldInput[$sRealFieldName] : '';
                    $aFieldList[$sMappedFieldName]['aValueList'] = array();
                    if (null !== $oFieldOptionList) {
                        while ($oFieldOption = $oFieldOptionList->Next()) {
                            $aFieldOption = array();
                            $aFieldOption['sName'] = $oFieldOption->GetName();
                            $aFieldOption['sValue'] = $oFieldOption->id;
                            $aFieldList[$sMappedFieldName]['aValueList'][] = $aFieldOption;
                        }
                    }
                    break;
            }
            if ($oFeedbackErrorList->FieldHasErrors($sRealFieldName)) {
                $translator = $this->getTranslationService();
                $aFieldList[$sMappedFieldName]['sError'] = $translator->trans('chameleon_system_core.module_feedback.required_field_missing');
            }
        }

        return $aFieldList;
    }

    /**
     * @return TranslatorInterface
     */
    private function getTranslationService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('translator');
    }
}
