<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgNewsletterMapper_PkgNewsletterModuleSingOutConfig extends AbstractViewMapper
{
    /**
     * {@inheritDoc}
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements)
    {
        $oRequirements->NeedsSourceObject('oObject', 'TdbPkgNewsletterModuleSignoutConfig');
        $oRequirements->NeedsSourceObject('sStepName', '');
    }

    /**
     * {@inheritDoc}
     */
    public function Accept(IMapperVisitorRestricted $oVisitor, $bCachingEnabled, IMapperCacheTriggerRestricted $oCacheTriggerManager)
    {
        /** @var $oNewsletterModuleSignOutConfig TdbPkgNewsletterModuleSignoutConfig */
        $oNewsletterModuleSignOutConfig = $oVisitor->GetSourceObject('oObject');
        if ($oNewsletterModuleSignOutConfig && $bCachingEnabled) {
            $oCacheTriggerManager->addTrigger($oNewsletterModuleSignOutConfig->table, $oNewsletterModuleSignOutConfig->id);
        }
        $sStepName = $oVisitor->GetSourceObject('sStepName');

        switch ($sStepName) {
            case 'Confirm':
                $oVisitor->SetMappedValue('sHeadLine', $oNewsletterModuleSignOutConfig->fieldSignoutConfirmTitle);
                $oVisitor->SetMappedValue('sText', $oNewsletterModuleSignOutConfig->GetTextField('signout_confirm_text'));
                break;
            case 'SignedOut':
                $oVisitor->SetMappedValue('sHeadLine', $oNewsletterModuleSignOutConfig->fieldSignedoutTitle);
                $oVisitor->SetMappedValue('sText', $oNewsletterModuleSignOutConfig->GetTextField('signedout_text'));
                break;
            case 'NoNewsToSignOut':
                $oVisitor->SetMappedValue('sHeadLine', $oNewsletterModuleSignOutConfig->fieldNoNewsletterSignedup);
                $oVisitor->SetMappedValue('sText', $oNewsletterModuleSignOutConfig->GetTextField('no_newsletter_signedup_text'));
                break;
            case 'SignOut':
            default:
                $oVisitor->SetMappedValue('sHeadLine', $oNewsletterModuleSignOutConfig->fieldSignoutTitle);
                $oVisitor->SetMappedValue('sText', $oNewsletterModuleSignOutConfig->GetTextField('signout_text'));
                break;
        }

        $oVisitor->SetMappedValue('sStepName', $sStepName);
    }
}
