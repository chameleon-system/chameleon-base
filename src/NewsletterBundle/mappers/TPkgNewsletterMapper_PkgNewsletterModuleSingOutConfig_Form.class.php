<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgNewsletterMapper_PkgNewsletterModuleSingOutConfig_Form extends AbstractViewMapper
{
    /**
     * {@inheritDoc}
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements)
    {
        $oRequirements->NeedsSourceObject('oObject', 'TdbPkgNewsletterModuleSignoutConfig');
        $oRequirements->NeedsSourceObject('oNewsletterUser', 'TdbPkgNewsletterUser', TdbPkgNewsletterUser::GetInstanceForActiveUser());
        $oRequirements->NeedsSourceObject('oUser', 'TdbDataExtranetUser', TdbDataExtranetUser::GetInstance());
        $oRequirements->NeedsSourceObject('sModuleSpotName');
        $oRequirements->NeedsSourceObject('oSignedInNewsletterList');
    }

    /**
     * {@inheritDoc}
     */
    public function Accept(IMapperVisitorRestricted $oVisitor, $bCachingEnabled, IMapperCacheTriggerRestricted $oCacheTriggerManager)
    {
        $oMessageManager = TCMSMessageManager::GetInstance();
        $oVisitor->SetMappedValue('sModuleSpotName', $oVisitor->GetSourceObject('sModuleSpotName'));
        $oSignedInNewsletterList = $oVisitor->GetSourceObject('oSignedInNewsletterList');
        $oVisitor->SetMappedValue('sMessageGeneral', $oMessageManager->RenderMessages(MTPkgNewsletterSignoutCore::INPUT_DATA_NAME));
        $oVisitor->SetMappedValue('sMessageNewsletterList', $oMessageManager->RenderMessages(MTPkgNewsletterSignoutCore::INPUT_DATA_NAME.'-newsletterout'));

        $oVisitor->SetMappedValue('sFieldNamesPrefix', MTPkgNewsletterSignoutCore::INPUT_DATA_NAME);
        $this->mapFormFields($oVisitor);
        $this->mapAvailableGroupList($oSignedInNewsletterList, $oVisitor);
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
        $aFieldEmail = array(
            'sError' => $oMessageManager->RenderMessages(MTPkgNewsletterSignoutCore::INPUT_DATA_NAME.'-newsletteremail'),
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
     * @param TdbPkgNewsletterGroupList $oSignedInNewsletterList
     * @param IMapperVisitorRestricted $oVisitor
     *
     * @return void
     */
    protected function mapAvailableGroupList($oSignedInNewsletterList, IMapperVisitorRestricted &$oVisitor)
    {
        /** @var $oNewsletterUser TdbPkgNewsletterUser */
        $oNewsletterUser = $oVisitor->GetSourceObject('oNewsletterUser');
        $aGroupList = array();
        while ($oGroup = &$oSignedInNewsletterList->Next()) {
            $aUserGroups = (null !== $oNewsletterUser) ? ($oSignedInNewsletterList->GetIdList()) : (array());
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
