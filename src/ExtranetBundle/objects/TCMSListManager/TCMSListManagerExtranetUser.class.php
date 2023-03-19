<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;

/**
 * manages the extranet user list.
/**/
class TCMSListManagerExtranetUser extends TCMSListManagerFullGroupTable
{
    /**
     * the function block for the buttons in the function column
     * adds a button to login as a extranet user (if cms user has the right to do that).
     *
     * @param string $id  - id of the current page
     * @param array  $row - all field/value pairs of the page
     *
     * @return string
     */
    public function CallBackFunctionBlock($id, $row)
    {
        $sReturnValue = parent::CallBackFunctionBlock($id, $row);

        $sReturnValue = substr($sReturnValue, 0, strrpos($sReturnValue, '<div id="functionTitle_'.$row['cmsident'].'" class="functionTitle">'));

        $aParameter = TGlobal::instance()->GetUserData(null, array('module_fnc', 'id', '_noModuleFunction', 'pagedef'));
        $aParameter['module_fnc'] = array(TGlobal::instance()->GetExecutingModulePointer()->sModuleSpotName => 'LoginAsExtranetUser');
        $aParameter['_noModuleFunction'] = 'true';
        $aParameter['id'] = $id;
        $aParameter['pagedef'] = 'tableeditor';
        $aParameter['tableid'] = $this->oTableConf->id;

        $sURL = PATH_CMS_CONTROLLER.'?'.$this->getUrlUtil()->getArrayAsUrl($aParameter);

        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

        if (true === $securityHelper->isGranted('CMS_RIGHT_ALLOW-LOGIN-AS-EXTRANET-USER')) {
            $sReturnValue .= "<a href=\"{$sURL}\" target=\"_blank\"><i class=\"fas fa-user-check\" onMouseOver=\"$('#functionTitle_'+".$row['cmsident'].").html('".TGlobal::Translate('chameleon_system_extranet.action.login_as_extranet_user')."');\" onMouseOut=\"$('#functionTitle_'+".$row['cmsident'].").html('');\"></i></a>";
        }

        $sReturnValue .= '<div id="functionTitle_'.$row['cmsident'].'" class="functionTitle"></div>';
        $sReturnValue .= '</div>';

        return $sReturnValue;
    }

    /**
     * @return UrlUtil
     */
    private function getUrlUtil()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.util.url');
    }
}
