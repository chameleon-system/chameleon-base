<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\UpdateCounterMigrationBundle\Exception\InvalidMigrationCounterException;
use Doctrine\DBAL\Connection;
use Symfony\Contracts\Translation\TranslatorInterface;

class MTLoginEndPoint extends TCMSModelBase
{
    public function Execute()
    {
        $this->data = parent::Execute();

        $this->data['username'] = '';
        $this->data['login'] = ''; // last logged in username
        if ($this->global->UserDataExists('login')) {
            $this->data['username'] = $this->global->GetUserData('login');
            $this->data['login'] = $this->data['username'];
        }

        $this->data['redirectParams'] = '';
        if ($this->global->UserDataExists('redirectParams')) {
            $this->data['redirectParams'] = $this->global->GetUserData('redirectParams');
        }

        $this->data['validBrowser'] = false;
        if (MTLogin::CheckBrowser()) {
            $this->data['validBrowser'] = true;
        } else {
            $this->data['errmsg'] = TGlobal::Translate('chameleon_system_core.cms_module_login.error_unsupported_browser');
        }

        if ($this->global->CMSUserDefined()) {
            /**
             * @psalm-suppress UndefinedInterfaceMethod
             * @FIXME The `HeaderRedirect` method only exists on 1 of the 2 interface implementations.
             */
            $this->controller->HeaderRedirect([]);
        }

        return $this->data;
    }

    /**
     * @return bool
     */
    public static function CheckBrowser()
    {
        $validBrowser = true;
        if (preg_match('/(?i)msie [6-8]/', $_SERVER['HTTP_USER_AGENT'])) { // Internet Explorer < 9.x
            $validBrowser = false;
        }

        return $validBrowser;
    }

    /**
     * @return void
     */
    private function redirectOnPendingUpdates()
    {
        try {
            $needsRedirect = \count(TCMSUpdateManager::GetInstance()->getAllUpdateFilesToProcess()) > 0;
        } catch (InvalidMigrationCounterException $e) {
            $needsRedirect = true;
        }

        if (true === $needsRedirect) {
            $this->getRedirect()->redirectToActivePage([
                'pagedef' => 'CMSUpdateManager',
                'module_fnc' => array($this->sModuleSpotName => 'RunUpdates'),
            ]);
        }
    }

    /**
     * @return Connection
     */
    private function getDatabaseConnection()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('database_connection');
    }

    /**
     * @return ICmsCoreRedirect
     */
    private function getRedirect()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.redirect');
    }

    /**
     * @return TranslatorInterface
     */
    private function getTranslator()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('translator');
    }
}
