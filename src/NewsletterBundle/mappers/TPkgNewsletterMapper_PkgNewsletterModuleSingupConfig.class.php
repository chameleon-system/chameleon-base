<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgNewsletterMapper_PkgNewsletterModuleSingupConfig extends AbstractViewMapper
{
    /**
     * {@inheritDoc}
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements)
    {
        $oRequirements->NeedsSourceObject('oObject', 'TdbPkgNewsletterModuleSignupConfig');
        $oRequirements->NeedsSourceObject('sStepName', '');
    }

    /**
     * {@inheritDoc}
     */
    public function Accept(IMapperVisitorRestricted $oVisitor, $bCachingEnabled, IMapperCacheTriggerRestricted $oCacheTriggerManager)
    {
        /** @var $oNewsletterModuleSignUpConfig TdbPkgNewsletterModuleSignupConfig */
        $oNewsletterModuleSignUpConfig = $oVisitor->GetSourceObject('oObject');
        if ($oNewsletterModuleSignUpConfig && $bCachingEnabled) {
            $oCacheTriggerManager->addTrigger($oNewsletterModuleSignUpConfig->table, $oNewsletterModuleSignUpConfig->id);
        }
        $sStepName = $oVisitor->GetSourceObject('sStepName');

        switch ($sStepName) {
            case 'Confirm':
                $oVisitor->SetMappedValue('sHeadLine', $oNewsletterModuleSignUpConfig->fieldConfirmTitle);
                $oVisitor->SetMappedValue('sText', $oNewsletterModuleSignUpConfig->GetTextField('confirm_text'));
                break;
            case 'SignedUp':
                $oVisitor->SetMappedValue('sHeadLine', $oNewsletterModuleSignUpConfig->fieldSignedupHeadline);
                $oVisitor->SetMappedValue('sText', $oNewsletterModuleSignUpConfig->GetTextField('signedup_text'));
                break;
            case 'NoNewsToSignUp':
                $oVisitor->SetMappedValue('sHeadLine', $oNewsletterModuleSignUpConfig->fieldNonewsignupTitle);
                $oVisitor->SetMappedValue('sText', $oNewsletterModuleSignUpConfig->GetTextField('nonewsignup_text'));
                break;
            case 'SignUp':
            default:
                $oVisitor->SetMappedValue('sHeadLine', $oNewsletterModuleSignUpConfig->fieldSignupHeadline);
                $oVisitor->SetMappedValue('sText', $oNewsletterModuleSignUpConfig->GetTextField('signup_text'));
                break;
        }

        $oVisitor->SetMappedValue('sStepName', $sStepName);
    }
}
