<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class MTMenuManager extends TCMSModelBase
{
    /**
     * {@inheritdoc}
     */
    public function Init()
    {
        $this->AddURLHistory();
    }

    /**
     * {@inheritdoc}
     */
    public function &Execute()
    {
        $this->data = parent::Execute();
        $this->RenderMenues();

        return $this->data;
    }

    public function AddURLHistory()
    {
        if ($this->AllowAddingURLToHistory()) {
            $this->global->GetURLHistory()->AddItem(array('pagedef' => $this->global->GetUserData('pagedef')), TGlobal::Translate('chameleon_system_core.cms_module_header.action_main_menu'));
        }
    }

    public function RenderMenues()
    {
        // fetch the three menues
        $this->data['oLeftMenu'] = new TCMSContentBox();
        /** @var $oLeftMenu TCMSContentBox */
        $this->data['oLeftMenu']->sLocation = 'left';
        $this->data['oLeftMenu']->Load();

        $this->data['oMiddleMenu'] = new TCMSContentBox();
        /** @var $oLeftMenu TCMSContentBox */
        $this->data['oMiddleMenu']->sLocation = 'middle';
        $this->data['oMiddleMenu']->Load();

        $this->data['oRightMenu'] = new TCMSContentBox();
        /** @var $oLeftMenu TCMSContentBox */
        $this->data['oRightMenu']->sLocation = 'right';
        $this->data['oRightMenu']->Load();
    }

    public function DefineInterface()
    {
        $externalFunctions = array('');
        $this->methodCallAllowed = array_merge($this->methodCallAllowed, $externalFunctions);
    }

    /**
     * {@inheritdoc}
     */
    public function GetHtmlHeadIncludes()
    {
        $aIncludes = parent::GetHtmlHeadIncludes();
        $aIncludes[] = '<link href="'.TGlobal::GetPathTheme().'/css/contentbox.css" rel="stylesheet" type="text/css" />';

        return $aIncludes;
    }

    /**
     * {@inheritdoc}
     */
    public function _AllowCache()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function _GetCacheParameters()
    {
        $aParameters = parent::_GetCacheParameters();
        if (!is_array($aParameters)) {
            $aParameters = array();
        }
        /** @var $oCMSUser TdbCmsUser */
        $oCMSUser = &TCMSUser::GetActiveUser();
        $oTdbCMSUser = TdbCmsUser::GetNewInstance();
        $oTdbCMSUser->Load($oCMSUser->id);

        /** @var $oBackendLanguage TdbCmsLanguage */
        $oBackendLanguage = $oTdbCMSUser->GetFieldCmsLanguage();

        $aParameters['sCMSUserId'] = $oTdbCMSUser->id;
        $aParameters['sBackendLanguageId'] = $oBackendLanguage->id;

        return $aParameters;
    }

    /**
     * {@inheritdoc}
     */
    public function _GetCacheTableInfos()
    {
        $aClearTriggers = parent::_GetCacheTableInfos();
        $aClearTriggers[] = array('table' => 'cms_tbl_conf', 'id' => '');
        $aClearTriggers[] = array('table' => 'cms_content_box', 'id' => '');
        $oCMSUser = &TCMSUser::GetActiveUser();
        $aClearTriggers[] = array('table' => 'cms_user', 'id' => $oCMSUser->id);
        $aClearTriggers[] = array('table' => 'cms_widget_task', 'id' => '');

        return $aClearTriggers;
    }
}
