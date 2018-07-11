<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\ActivePageServiceInterface;

class TPkgMultiModule_CMSTPLModuleInstance extends TPkgMultiModule_CMSTPLModuleInstanceAutoParent
{
    /**
     * Returns the URL to do ajax call with.
     *
     * @param bool $bGetAsJSFunction
     *
     * @return string
     *
     * @deprecated - use TdbPkgMultiModuleSetItem::GetAjaxURLForContainingModule() instead
     */
    public function GetAjaxURLForContainingModule($bGetAsJSFunction = false)
    {
        $oGlobal = $this->getGlobal();
        $oExecutingModule = $oGlobal->GetExecutingModulePointer();
        $aAdditionalParameters = array('module_fnc['.$oExecutingModule->sModuleSpotName.']' => 'ExecuteAjaxCall', '_fnc' => 'RenderModuleAjax', 'sShowModuleInstanceId' => $this->id);
        $sLink = $this->getActivePageService()->getLinkToActivePageRelative($aAdditionalParameters);
        if ($bGetAsJSFunction) {
            $sLink = "CHAMELEONPKGMULTIMODULE.GetPkgMultiModuleAjaxCall('{$sLink}', CHAMELEONPKGMULTIMODULE.RenderModule)";
        }

        return $sLink;
    }

    /**
     * @return TGlobal
     */
    private function getGlobal()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.global');
    }

    /**
     * @return ActivePageServiceInterface
     */
    private function getActivePageService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.active_page_service');
    }
}
