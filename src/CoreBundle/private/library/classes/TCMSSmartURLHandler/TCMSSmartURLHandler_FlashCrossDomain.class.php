<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;

/**
 * if we use static server flash files will load from this domain. Then the flash file has no rights
 * to get data from different domains if not defined in crossdomain.xml
 * This URL handler generates a crossdomain.xml including all domains configured in cms.
 *
 * @deprecated since 6.2.0 - Flash support will be removed in Chameleon 7.0.
 */
class TCMSSmartURLHandler_FlashCrossDomain extends TCMSSmartURLHandler
{
    /**
     * Echos crossdomain.xml if requested.
     *
     * @return bool
     */
    public function GetPageDef()
    {
        $iPageId = false;
        $oURLData = &TCMSSmartURLData::GetActive();
        $sPath = $oURLData->sRelativeURL;
        if (false != strpos($sPath, 'crossdomain.xml')) {
            if (!ob_start('ob_gzhandler')) {
                ob_start();
            }
            echo $this->GenerateCrossDomainXML();
            header('Content-Type: text/xml');
            header('Content-Length: '.ob_get_length());
            ob_end_flush();
            exit(0);
        }

        return $iPageId;
    }

    /**
     * Generates crossdomain.xml from active portal domains.
     *
     * @return string
     */
    protected function GenerateCrossDomainXML()
    {
        $sXML = '<?xml version="1.0"?>
<!DOCTYPE cross-domain-policy SYSTEM "http://www.macromedia.com/xml/dtds/cross-domain-policy.dtd">
  <cross-domain-policy>
';
        $oPortal = $this->getPortalDomainService()->getActivePortal();
        $oPortalDomainList = $oPortal->GetFieldCmsPortalDomainsList();
        while ($oPortalDomain = $oPortalDomainList->Next()) {
            $sDomain = $oPortalDomain->fieldName;
            if (!_DEVELOPMENT_MODE) {
                if (!$oPortalDomain->IsDevelopmentDomain()) {
                    $sXML .= '    <allow-access-from domain="'.TGlobal::OutHTML($sDomain).'" />'."\n";
                    $sXML .= '    <allow-access-from domain="*.'.TGlobal::OutHTML($sDomain).'" />'."\n";
                }
            } else {
                $sXML .= '    <allow-access-from domain="'.TGlobal::OutHTML($sDomain).'" />'."\n";
                $sXML .= '    <allow-access-from domain="*.'.TGlobal::OutHTML($sDomain).'" />'."\n";
            }
        }
        $aStaticURLs = TGlobal::GetStaticURLPrefix();
        if (!is_array($aStaticURLs)) {
            $aStaticURLs = array($aStaticURLs);
        }
        foreach ($aStaticURLs as $sStaticURL) {
            $sStaticDomain = $this->GetStaticURLAsDomain($sStaticURL);
            $sXML .= '    <allow-access-from domain="'.TGlobal::OutHTML($sStaticDomain).'" />'."\n";
            $sXML .= '    <allow-access-from domain="*.'.TGlobal::OutHTML($sStaticDomain).'" />'."\n";
        }
        $sXML .= '  </cross-domain-policy>';

        return $sXML;
    }

    /**
     * Returns static domain.
     *
     * @return mixed|string
     */
    protected function GetStaticURLAsDomain($sStaticURL)
    {
        $sStaticDomain = $sStaticURL;
        $sStaticDomain = str_replace('http://', '', $sStaticDomain);
        if ('www.' == substr($sStaticDomain, 0, 4)) {
            $sStaticDomain = substr($sStaticDomain, 4);
        }

        return $sStaticDomain;
    }

    private function getPortalDomainService(): PortalDomainServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.portal_domain_service');
    }
}
