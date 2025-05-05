<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\PageServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;

/**
 * module generates an xml listing of a module.
 * requires as input:
 *   instanceId - instance of the module to render
 *   view - view to use
 *   refererPageId - from which page was the call made
 *   any other parameters that the module may expect to select the correct items.
 * /**/
class MTCMSExportModuleCore extends TUserCustomModelBase
{
    /**
     * a module instance (the list).
     *
     * @var TCMSTPLModuleInstance
     */
    protected $oListModule;
    protected $iInstId;
    protected $sView;

    public const SESSION_INFO_NAME = '_moduleexportallowview';

    public function Init()
    {
        // allow view? if not, redirect to home page :)
        $this->iInstId = $this->global->GetUserData('instanceId');
        $this->sView = $this->global->GetUserData('view');
        $bAllowExport = (array_key_exists(self::SESSION_INFO_NAME, $_SESSION) && array_key_exists($this->iInstId.$this->sView, $_SESSION[self::SESSION_INFO_NAME]));
        $bAllowExport = ($bAllowExport && $_SESSION[self::SESSION_INFO_NAME][$this->iInstId.$this->sView]);
        if (!$bAllowExport) {
            $this->getRedirectService()->redirect($this->getPageService()->getLinkToPortalHomePageAbsolute());
        }

        parent::Init();
        $this->oListModule = new TCMSTPLModuleInstance();
    }

    public function Execute()
    {
        parent::Execute();
        $this->oListModule->Load($this->iInstId);
        $this->oListModule->Init('tmpspot', $this->sView);

        $this->data['sContent'] = $this->oListModule->RenderModule();

        return $this->data;
    }

    private function getPageService(): PageServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.page_service');
    }

    private function getRedirectService(): ICmsCoreRedirect
    {
        return ServiceLocator::get('chameleon_system_core.redirect');
    }
}
