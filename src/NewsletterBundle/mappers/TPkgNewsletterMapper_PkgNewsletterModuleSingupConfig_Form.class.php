<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgNewsletterMapper_PkgNewsletterModuleSingupConfig_Form extends AbstractViewMapper
{
    /**
     * {@inheritDoc}
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements)
    {
        $oRequirements->NeedsSourceObject('oObject', 'TdbPkgNewsletterModuleSignupConfig');
        $oRequirements->NeedsSourceObject('oNewsletterUser', 'TdbPkgNewsletterUser', TdbPkgNewsletterUser::GetInstanceForActiveUser());
        $oRequirements->NeedsSourceObject('oUser', 'TdbDataExtranetUser', TdbDataExtranetUser::GetInstance());
        $oRequirements->NeedsSourceObject('sNewsletterLink');
        $oRequirements->NeedsSourceObject('sModuleSpotName');
    }

    /**
     * {@inheritDoc}
     */
    public function Accept(IMapperVisitorRestricted $oVisitor, $bCachingEnabled, IMapperCacheTriggerRestricted $oCacheTriggerManager)
    {
        $oMessageManager = TCMSMessageManager::GetInstance();

        $oVisitor->SetMappedValue('sNewsletterLink', $oVisitor->GetSourceObject('sNewsletterLink'));
        $oVisitor->SetMappedValue('sModuleSpotName', $oVisitor->GetSourceObject('sModuleSpotName'));

        $oVisitor->SetMappedValue('sMessageGeneral', $oMessageManager->RenderMessages(MTPkgNewsletterSignupCore::INPUT_DATA_NAME));
        $oVisitor->SetMappedValue('sMessageNewsletterList', $oMessageManager->RenderMessages(MTPkgNewsletterSignupCore::INPUT_DATA_NAME.'-newsletter'));

        $oVisitor->SetMappedValue('sFieldNamesPrefix', MTPkgNewsletterSignupCore::INPUT_DATA_NAME);
        $this->mapFormFields($oVisitor);
        $this->mapAvailableGroupList($oVisitor);
    }

    /**
     * @param IMapperVisitorRestricted $oVisitor
     *
     * @return void
     */
    protected function mapFormFields(IMapperVisitorRestricted &$oVisitor)
    {
        /** @var $oNewsletterUser TdbPkgNewsletterUser */
        $oNewsletterUser = $oVisitor->GetSourceObject('oNewsletterUser');
        /** @var $oUser TdbDataExtranetUser */
        $oUser = $oVisitor->GetSourceObject('oUser');

        $oMessageManager = TCMSMessageManager::GetInstance();

        $aFieldSalutation = array();
        $aFieldSalutation['sValue'] = ('' != $oNewsletterUser->fieldDataExtranetSalutationId) ? ($oNewsletterUser->fieldDataExtranetSalutationId) : ($oUser->fieldDataExtranetSalutationId);
        $aValueList = array();
        $oSalutationList = &TdbDataExtranetSalutationList::GetList();
        while ($oSalutation = &$oSalutationList->Next()) {
            $aValueList[] = array(
                'sValue' => $oSalutation->id,
                'sName' => $oSalutation->GetName(),
            );
        }
        $aFieldSalutation['aValueList'] = $aValueList;
        $aFieldSalutation['sError'] = $oMessageManager->RenderMessages(MTPkgNewsletterSignupCore::INPUT_DATA_NAME.'-data_extranet_salutation_id');
        $oVisitor->SetMappedValue('aFieldSalutation', $aFieldSalutation);

        $aFieldFirstName = array(
            'sError' => $oMessageManager->RenderMessages(MTPkgNewsletterSignupCore::INPUT_DATA_NAME.'-firstname'),
            'sValue' => ('' != $oNewsletterUser->fieldFirstname) ? ($oNewsletterUser->fieldFirstname) : ($oUser->fieldFirstname),
        );
        $oVisitor->SetMappedValue('aFieldFirstName', $aFieldFirstName);

        $aFieldLastName = array(
            'sError' => $oMessageManager->RenderMessages(MTPkgNewsletterSignupCore::INPUT_DATA_NAME.'-lastname'),
            'sValue' => ('' != $oNewsletterUser->fieldLastname) ? ($oNewsletterUser->fieldLastname) : ($oUser->fieldLastname),
        );
        $oVisitor->SetMappedValue('aFieldLastName', $aFieldLastName);

        $aFieldCompany = array(
            'sError' => $oMessageManager->RenderMessages(MTPkgNewsletterSignupCore::INPUT_DATA_NAME.'-company'),
            'sValue' => ('' != $oNewsletterUser->fieldCompany) ? ($oNewsletterUser->fieldCompany) : ($oUser->fieldCompany),
        );
        $oVisitor->SetMappedValue('aFieldCompany', $aFieldCompany);

        $aFieldEmail = array(
            'sError' => $oMessageManager->RenderMessages(MTPkgNewsletterSignupCore::INPUT_DATA_NAME.'-email'),
            'sValue' => ('' != $oNewsletterUser->fieldEmail) ? ($oNewsletterUser->fieldEmail) : ($oUser->fieldEmail),
        );
        $bShowEmailField = true;
        if ($oUser->IsLoggedIn()) {
            $bShowEmailField = false;
        }
        $oVisitor->SetMappedValue('aFieldEmail', $aFieldEmail);
        $oVisitor->SetMappedValue('bShowEmailField', $bShowEmailField);
    }

    /**
     * @param IMapperVisitorRestricted $oVisitor
     *
     * @return void
     */
    protected function mapAvailableGroupList(IMapperVisitorRestricted &$oVisitor)
    {
        /** @var $oNewsletterModuleSignUpConfig TdbPkgNewsletterModuleSignupConfig */
        $oNewsletterModuleSignUpConfig = $oVisitor->GetSourceObject('oObject');
        /** @var $oNewsletterUser TdbPkgNewsletterUser */
        $oNewsletterUser = $oVisitor->GetSourceObject('oNewsletterUser');

        $aGroupList = array();
        $oGroupsAvailableForSignUp = $oNewsletterModuleSignUpConfig->GetFieldPkgNewsletterGroupList();
        while ($oGroup = &$oGroupsAvailableForSignUp->Next()) {
            $aUserGroups = (null !== $oNewsletterUser) ? ($oNewsletterUser->GetSignedInNewsletterList()->GetIdList()) : (array());
            $aGroupList[] = array(
                'id' => $oGroup->id,
                'sName' => $oGroup->GetName(),
                'sError' => '',
                'bIsChecked' => (is_array($aUserGroups) && 0 < count($aUserGroups) && in_array($oGroup->id, $aUserGroups)),
            );
        }
        $oVisitor->SetMappedValue('aGroupList', $aGroupList);
    }
}
