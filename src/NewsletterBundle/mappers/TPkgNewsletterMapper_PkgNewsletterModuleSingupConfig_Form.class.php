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
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements): void
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
    public function Accept(IMapperVisitorRestricted $oVisitor, $bCachingEnabled, IMapperCacheTriggerRestricted $oCacheTriggerManager): void
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
     * @return void
     */
    protected function mapFormFields(IMapperVisitorRestricted $oVisitor)
    {
        /** @var TdbPkgNewsletterUser $oNewsletterUser */
        $oNewsletterUser = $oVisitor->GetSourceObject('oNewsletterUser');
        /** @var TdbDataExtranetUser $oUser */
        $oUser = $oVisitor->GetSourceObject('oUser');

        $oMessageManager = TCMSMessageManager::GetInstance();

        $aFieldSalutation = [];
        $aFieldSalutation['sValue'] = ('' != $oNewsletterUser->fieldDataExtranetSalutationId) ? ($oNewsletterUser->fieldDataExtranetSalutationId) : ($oUser->fieldDataExtranetSalutationId);
        $aValueList = [];
        $oSalutationList = TdbDataExtranetSalutationList::GetList();
        while ($oSalutation = $oSalutationList->Next()) {
            $aValueList[] = [
                'sValue' => $oSalutation->id,
                'sName' => $oSalutation->GetName(),
            ];
        }
        $aFieldSalutation['aValueList'] = $aValueList;
        $aFieldSalutation['sError'] = $oMessageManager->RenderMessages(MTPkgNewsletterSignupCore::INPUT_DATA_NAME.'-data_extranet_salutation_id');
        $oVisitor->SetMappedValue('aFieldSalutation', $aFieldSalutation);

        $aFieldFirstName = [
            'sError' => $oMessageManager->RenderMessages(MTPkgNewsletterSignupCore::INPUT_DATA_NAME.'-firstname'),
            'sValue' => ('' != $oNewsletterUser->fieldFirstname) ? ($oNewsletterUser->fieldFirstname) : ($oUser->fieldFirstname),
        ];
        $oVisitor->SetMappedValue('aFieldFirstName', $aFieldFirstName);

        $aFieldLastName = [
            'sError' => $oMessageManager->RenderMessages(MTPkgNewsletterSignupCore::INPUT_DATA_NAME.'-lastname'),
            'sValue' => ('' != $oNewsletterUser->fieldLastname) ? ($oNewsletterUser->fieldLastname) : ($oUser->fieldLastname),
        ];
        $oVisitor->SetMappedValue('aFieldLastName', $aFieldLastName);

        $aFieldCompany = [
            'sError' => $oMessageManager->RenderMessages(MTPkgNewsletterSignupCore::INPUT_DATA_NAME.'-company'),
            'sValue' => ('' != $oNewsletterUser->fieldCompany) ? ($oNewsletterUser->fieldCompany) : ($oUser->fieldCompany),
        ];
        $oVisitor->SetMappedValue('aFieldCompany', $aFieldCompany);

        $aFieldEmail = [
            'sError' => $oMessageManager->RenderMessages(MTPkgNewsletterSignupCore::INPUT_DATA_NAME.'-email'),
            'sValue' => ('' != $oNewsletterUser->fieldEmail) ? ($oNewsletterUser->fieldEmail) : ($oUser->fieldEmail),
        ];
        $bShowEmailField = true;
        if ($oUser->IsLoggedIn()) {
            $bShowEmailField = false;
        }
        $oVisitor->SetMappedValue('aFieldEmail', $aFieldEmail);
        $oVisitor->SetMappedValue('bShowEmailField', $bShowEmailField);
    }

    /**
     * @return void
     */
    protected function mapAvailableGroupList(IMapperVisitorRestricted $oVisitor)
    {
        /** @var TdbPkgNewsletterModuleSignupConfig $oNewsletterModuleSignUpConfig */
        $oNewsletterModuleSignUpConfig = $oVisitor->GetSourceObject('oObject');
        /** @var TdbPkgNewsletterUser $oNewsletterUser */
        $oNewsletterUser = $oVisitor->GetSourceObject('oNewsletterUser');

        $aGroupList = [];
        $oGroupsAvailableForSignUp = $oNewsletterModuleSignUpConfig->GetFieldPkgNewsletterGroupList();
        while ($oGroup = $oGroupsAvailableForSignUp->Next()) {
            $aUserGroups = (null !== $oNewsletterUser) ? ($oNewsletterUser->GetSignedInNewsletterList()->GetIdList()) : ([]);
            $aGroupList[] = [
                'id' => $oGroup->id,
                'sName' => $oGroup->GetName(),
                'sError' => '',
                'bIsChecked' => (is_array($aUserGroups) && 0 < count($aUserGroups) && in_array($oGroup->id, $aUserGroups)),
            ];
        }
        $oVisitor->SetMappedValue('aGroupList', $aGroupList);
    }
}
