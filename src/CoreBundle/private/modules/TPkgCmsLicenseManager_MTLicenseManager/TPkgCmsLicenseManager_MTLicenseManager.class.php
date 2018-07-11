<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgCmsLicenseManager_MTLicenseManager extends TCMSModelBase
{
    protected function DefineInterface()
    {
        parent::DefineInterface();
        $this->methodCallAllowed[] = 'enterLicense';
    }

    protected function enterLicense()
    {
        $sLicense = trim($this->global->GetUserData('key'));
        $sOwner = trim($this->global->GetUserData('owner'));
        $sDomainList = trim($this->global->GetUserData('domain_list'));
        $bInputInvalid = false;
        if (empty($sLicense)) {
            $oMsgManager = TCMSMessageManager::GetInstance();
            $oMsgManager->AddMessage('LICENSE-KEY', 'TABLEEDITOR_FIELD_IS_MANDATORY', array('sFieldTitle' => TGlobal::Translate('Lizenz-Schlüssel')));
            $bInputInvalid = true;
        }
        if (empty($sOwner)) {
            $oMsgManager = TCMSMessageManager::GetInstance();
            $oMsgManager->AddMessage('LICENSE-OWNER', 'TABLEEDITOR_FIELD_IS_MANDATORY', array('sFieldTitle' => TGlobal::Translate('Lizenz-Nehmer')));
            $bInputInvalid = true;
        }
        if (empty($sDomainList)) {
            $oMsgManager = TCMSMessageManager::GetInstance();
            $oMsgManager->AddMessage('LICENSE-DOMAIN-LIST', 'TABLEEDITOR_FIELD_IS_MANDATORY', array('sFieldTitle' => TGlobal::Translate('Domain Liste')));
            $bInputInvalid = true;
        }

        if ($bInputInvalid) {
            return false;
        }
        $oLicenseManager = new TPkgCmsLicenseManager();
        if (false === $oLicenseManager->hasValidFormat($sLicense)) {
            $oMsgManager = TCMSMessageManager::GetInstance();
            $oMsgManager->AddMessage('LICENSE-KEY', 'PKGCMSLICENSEMANAGER_INVALID_KEY');

            return false;
        }

        $oLicense = $oLicenseManager->addLicense($sOwner, $sDomainList, $sLicense, date('Y-m-d H:i:s'));
        if (null === $oLicense) {
            $oMsgManager = TCMSMessageManager::GetInstance();
            $oMsgManager->AddMessage('LICENSE-KEY', 'PKGCMSLICENSEMANAGER_INVALID_KEY');

            return false;
        }

        $this->controller->HeaderURLRedirect(PATH_CMS_CONTROLLER);

        return true;
    }
}
