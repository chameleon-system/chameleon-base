<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\ExtranetBundle\Interfaces\ExtranetUserProviderInterface;

class MTExtranetMyAccountCore extends TUserCustomModelBase
{
    public const MSG_BASE_NAME = 'msgMTExtranetMyAccountCore';

    /**
     * config data for the module.
     *
     * @var TdbDataExtranetModuleMyAccount
     */
    protected $oModuleConfig;

    /**
     * @var bool
     */
    protected $bAllowHTMLDivWrapping = true;

    public function Init()
    {
        parent::Init();

        $this->oModuleConfig = TdbDataExtranetModuleMyAccount::GetNewInstance();
        $this->oModuleConfig->LoadFromField('cms_tpl_module_instance_id', $this->instanceID);
    }

    public function Execute()
    {
        parent::Execute();
        $user = $this->getExtranetUserProvider()->getActiveUser();
        $this->data['oUser'] = $user;
        $this->data['oModuleConfig'] = $this->oModuleConfig;

        $aCustomVars = $user->sqlData;
        $aCustomVars['salutation'] = '';
        $aCustomVars['country'] = '';
        $oSalutation = $user->GetFieldDataExtranetSalutation();
        if (!is_null($oSalutation)) {
            $aCustomVars['salutation'] = $oSalutation->GetName();
        }

        $oCountry = $user->GetFieldDataCountry();
        if (!is_null($oCountry)) {
            $aCustomVars['country'] = $oCountry->GetName();
        }

        $this->data['sIntroText'] = $this->oModuleConfig->GetTextField('intro', 600, false, $aCustomVars);

        return $this->data;
    }

    /**
     * @return ExtranetUserProviderInterface
     */
    private function getExtranetUserProvider()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_extranet.extranet_user_provider');
    }
}
