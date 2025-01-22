<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSSmartURLHandler_RegistrationConfirm extends TCMSSmartURLHandler
{
    public function GetPageDef()
    {
        $iPageId = false;
        $oURLData = TCMSSmartURLData::GetActive();
        $sPath = $oURLData->sRelativeURL;
        if ('/' == substr($sPath, 0, 1)) {
            $sPath = substr($sPath, 1);
        }
        $aParts = explode('/', $sPath);
        $aParts = $this->CleanPath($aParts);
        if ('key' == $aParts[0]) {
            $oNode = new TCMSTreeNode();
            $oExtranetConf = TdbDataExtranet::GetNewInstance();
            $oExtranetConf->LoadFromField('cms_portal_id', $oURLData->iPortalId);
            $oNode->Load($oExtranetConf->sqlData['node_confirm_registration']);
            $iPageId = $oNode->GetLinkedPage();
            $this->aCustomURLParameters['module_fnc'][$oExtranetConf->fieldExtranetSpotName] = 'ConfirmUser';
            $this->aCustomURLParameters['key'] = $aParts[1];
        }

        return $iPageId;
    }
}
