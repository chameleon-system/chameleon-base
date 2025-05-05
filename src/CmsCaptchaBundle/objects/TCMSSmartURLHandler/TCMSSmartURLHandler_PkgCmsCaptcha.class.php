<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSSmartURLHandler_PkgCmsCaptcha extends TCMSSmartURLHandler
{
    public function GetPageDef()
    {
        $iPageId = false;
        $oSmartURL = TCMSSmartURLData::GetActive();

        if (strpos($oSmartURL->sRelativeURL, TdbPkgCmsCaptcha::URL_IDENTIFIER)) {
            $aParts = explode('/', $oSmartURL->sRelativeURL);
            $aParts = $this->CleanPath($aParts);
            if (3 == count($aParts)) {
                $sCaptchaId = $aParts[1];
                $sCaptchaIdentifier = $aParts[2];
                $oCaptcha = TdbPkgCmsCaptcha::GetInstanceFromCmsIdent(intval($sCaptchaId));
                if ($oCaptcha) {
                    $oCaptcha->GenerateNewCaptchaImage($sCaptchaIdentifier, $oSmartURL->aParameters);
                    exit(0);
                }
            }
        }

        return $iPageId;
    }
}
