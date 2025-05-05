<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\SystemPageServiceInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class TCMSSmartURLHandler_PkgCommentReport extends TCMSSmartURLHandler
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
        $oPortal = TdbCmsPortal::GetNewInstance();
        $oPortal->Load($oURLData->iPortalId);

        try {
            $CompareLink = $this->getSystemPageService()->getLinkToSystemPageRelative('announcecomment', [], $oPortal);
        } catch (RouteNotFoundException $e) {
            return false;
        }
        if ('/' == substr($CompareLink, 0, 1)) {
            $CompareLink = substr($CompareLink, 1);
        }
        $aCompareLink = explode('/', $CompareLink);
        $aCompareLink = $this->CleanPath($aCompareLink);

        $bIsCorrect = false;
        for ($i = 0; $i < count($aCompareLink); ++$i) {
            if ($aCompareLink[$i] == $aParts[$i]) {
                $bIsCorrect = true;
            } else {
                $bIsCorrect = false;
                break;
            }
        }
        if ($bIsCorrect) {
            $oPortal = TdbCmsPortal::GetNewInstance();
            $oPortal->Load($oURLData->iPortalId);
            $iNodeId = $oPortal->GetSystemPageNodeId('announcecomment');
            $iPageId = static::GetNodePage($iNodeId);
            if (count($aParts) > 2) {
                $this->aCustomURLParameters[TdbPkgComment::URL_NAME_ID] = $aParts[2];
            }
        }

        return $iPageId;
    }

    /**
     * @return SystemPageServiceInterface
     */
    private function getSystemPageService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.system_page_service');
    }
}
