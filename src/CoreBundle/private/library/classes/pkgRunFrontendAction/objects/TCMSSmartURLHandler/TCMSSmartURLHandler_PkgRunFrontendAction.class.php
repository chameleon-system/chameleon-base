<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSSmartURLHandler_PkgRunFrontendAction extends TCMSSmartURLHandler
{
    /**
     * this method should parse the url and check which page matches
     * it should convert url parts to GET parameters by using aCustomURLParameters.
     */
    public function GetPageDef()
    {
        $iPageId = false;
        $oURLData = TCMSSmartURLData::GetActive();
        $aParts = explode('/', $oURLData->sRelativeURL);
        $aParts = $this->CleanPath($aParts);
        if (0 === count($aParts)) {
            return $iPageId;
        }

        if (TdbPkgRunFrontendAction::URL_IDENTIFIER == substr($aParts[0], 0, strlen(TdbPkgRunFrontendAction::URL_IDENTIFIER))) {
            $sKey = substr($aParts[0], strlen(TdbPkgRunFrontendAction::URL_IDENTIFIER));
            if (!empty($sKey)) {
                $oAction = TdbPkgRunFrontendAction::GetNewInstance();
                if ($oAction->LoadFromField('random_key', $sKey)) {
                    $oStatus = new TPkgRunFrontendActionStatus();
                    if ($oAction->isValid()) {
                        $oAtomicLock = new AtomicLock();
                        $oLock = $oAtomicLock->acquireLock('pkgRunFrontendAction'.$sKey);
                        if (null !== $oLock) {
                            $oLanguage = $oAction->GetFieldCmsLanguage();
                            if ($oLanguage) {
                                $oLanguage->TargetLanguageSimulation(true);
                            }
                            $oStatus = $oAction->runAction();
                            $oLock->release();
                        }
                    } else {
                        $oAction->AllowEditByAll(true);
                        $oAction->Delete();
                    }
                    echo json_encode($oStatus);
                    exit(0);
                }
            }
        }

        return $iPageId;
    }
}
