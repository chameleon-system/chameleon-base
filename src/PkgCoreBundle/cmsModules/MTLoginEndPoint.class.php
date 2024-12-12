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

/**
 * @deprecated - moved to ChameleonSystemCmsBackendBundle
 */
class MTLoginEndPoint extends TCMSModelBase
{
    public function Init()
    {
        parent::Init();

        $moduleFunction = $this->global->GetUserData('module_fnc');

        if (is_array($moduleFunction) && 'Logout' === $moduleFunction['contentmodule']) {
            return;
        }

        if ($this->global->CMSUserDefined()) {
            $this->getRedirectService()->redirectToActivePage([]);
        }
    }

    public function Execute()
    {
        $this->data = parent::Execute();

        $this->data['username'] = $this->global->GetUserData('username');

        $this->data['redirectParams'] = '';
        if ($this->global->UserDataExists('redirectParams')) {
            $this->data['redirectParams'] = $this->global->GetUserData('redirectParams');
        }

        if ($this->global->CMSUserDefined()) {
            $this->getRedirectService()->redirectToActivePage([]);
        }

        return $this->data;
    }

    private function getRedirectService(): ICmsCoreRedirect
    {
        return ServiceLocator::get('chameleon_system_core.redirect');
    }
}
