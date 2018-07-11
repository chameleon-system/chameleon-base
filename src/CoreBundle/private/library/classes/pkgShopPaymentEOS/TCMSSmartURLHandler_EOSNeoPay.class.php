<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Response;

/**
 * @deprecated since 6.2.0 - no longer used.
 */
class TCMSSmartURLHandler_EOSNeoPay extends TCMSSmartURLHandler_ShopBasketSteps
{
    public function GetPageDef()
    {
        $iPageId = false;
        $oURLData = &TCMSSmartURLData::GetActive();

        $iPos = strpos($oURLData->sRelativeURL, TShopPaymentHandler_EOSNeoPay::URL_IDENTIFIER);
        if (false !== $iPos) {
            $sPayload = substr($oURLData->sRelativeURL, $iPos + strlen(TShopPaymentHandler_EOSNeoPay::URL_IDENTIFIER));
            $sPayload = trim($sPayload, '/');
            $aPayload = explode('/', $sPayload);
            if (count($aPayload) > 0) {
                $oURLData->sRelativeURL = substr($oURLData->sRelativeURL, 0, $iPos);
                $oURLData->sRelativeFullURL = $oURLData->sRelativeURL;
                if (!empty($oURLData->sLanguageIdentifier)) {
                    $oURLData->sRelativeFullURL = '/'.$oURLData->sLanguageIdentifier.$oURLData->sRelativeFullURL;
                }
                if (!empty($oURLData->sRelativeURLPortalIdentifier)) {
                    $oURLData->sRelativeFullURL = '/'.$oURLData->sRelativeURLPortalIdentifier.$oURLData->sRelativeFullURL;
                }

                if ('return_from_form' == $aPayload[0]) {
                    $aRedirectParameter = $oURLData->aParameters;
                    if ('spot_' == substr($aPayload[2], 0, 5)) {
                        $sSpot = substr($aPayload[2], 5);
                        $aRedirectParameter['module_fnc'] = array($sSpot => 'PostProcessExternalPaymentHandlerHook');
                    }
                    $sURL = $oURLData->sRelativeFullURL.'?'.str_replace('&amp;', '&', TTools::GetArrayAsURL($aRedirectParameter));
                    if ('breakout' == $aPayload[1]) {
                        $aTranslationParams = array(
                            '%linkstart%' => '<a href="'.$sURL.'" target="_top" onclick="top.window.location.href = \''.$sURL.'\';return false;">',
                            '%linkend%' => '</a>',
                        );
                        echo '<html>
                                <head>
                                    <script type="text/javascript">top.window.location.href = \''.$sURL.'\';</script>
                                </head>
                                <body>
                                    '.TGlobal::Translate('chameleon_system_core.text.redirect_text', $aTranslationParams).'
                                </body>
                            </html>';
                    } else {
                        $this->getRedirect()->redirect($sURL, Response::HTTP_MOVED_PERMANENTLY);
                    }
                    exit;
                } elseif ('ipn' == $aPayload[0]) {
                    //ipn is currently not implemented because we only need it if we have delayed payment
                }
            }
        }

        return $iPageId;
    }
}
