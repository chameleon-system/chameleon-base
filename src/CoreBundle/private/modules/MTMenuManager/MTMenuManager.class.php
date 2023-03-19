<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CmsBackendBundle\BackendSession\BackendSessionInterface;
use ChameleonSystem\CoreBundle\Service\BackendBreadcrumbServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;

/**
 * @deprecated since 6.3.0 - classic main menu will be removed in a future Chameleon release
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
    public function Execute()
    {
        $this->data = parent::Execute();
        $this->RenderMenues();

        return $this->data;
    }

    public function AddURLHistory()
    {
        if ($this->AllowAddingURLToHistory()) {
            $breadcrumb = $this->getBreadcrumbService()->getBreadcrumb();
            $breadcrumb->AddItem(array('pagedef' => $this->global->GetUserData('pagedef')), TGlobal::Translate('chameleon_system_core.cms_module_header.action_main_menu'));
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

        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);


        $aParameters['sCMSUserId'] = $securityHelper->getUser()?->getId();
        $aParameters['sBackendLanguageId'] = $securityHelper->getUser()?->getCmsLanguageId();

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
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
        $aClearTriggers[] = array('table' => 'cms_user', 'id' => $securityHelper->getUser()?->getId());
        $aClearTriggers[] = array('table' => 'cms_widget_task', 'id' => '');

        return $aClearTriggers;
    }

    private function getBreadcrumbService(): BackendBreadcrumbServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.service.backend_breadcrumb');
    }
}
