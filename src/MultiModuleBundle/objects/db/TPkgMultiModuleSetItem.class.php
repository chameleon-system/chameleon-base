<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgMultiModuleSetItem extends TPkgMultiModuleSetItemAutoParent
{
    /**
     * Returns the URL to do ajax call with.
     *
     * @param bool $bGetAsJSFunction
     *
     * @return string
     */
    public function GetAjaxURLForContainingModule($bGetAsJSFunction = false)
    {
        $oGlobal = TGlobal::instance();
        $oExecutingModule = $oGlobal->GetExecutingModulePointer();
        $aAdditionalParameters = ['module_fnc['.$oExecutingModule->sModuleSpotName.']' => 'ExecuteAjaxCall', '_fnc' => 'RenderModuleAjax', 'sShowModuleInstanceId' => $this->fieldCmsTplModuleInstanceId];
        $sLink = '?'.TTools::GetArrayAsURL($aAdditionalParameters);
        if ($bGetAsJSFunction) {
            $sLink = "CHAMELEONPKGMULTIMODULE.GetPkgMultiModuleAjaxCall('{$sLink}', CHAMELEONPKGMULTIMODULE.RenderModule)";
        }

        return $sLink;
    }
}
