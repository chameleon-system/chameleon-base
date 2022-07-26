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
    public function &Execute()
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

    protected function DefineInterface()
    {
        $externalFunctions = array('Login', 'Logout');
        $this->methodCallAllowed = array_merge($this->methodCallAllowed, $externalFunctions);
    }

    /**
     * Check login request. if the credentials check out, we redirect to the
     * main page, else we stay at the login page.
     *
     * @return void
     */
    public function Login()
    {
        $username = $this->global->GetUserData('username');
        $sLastLoggedInUser = $this->global->GetUserData('login');
        $password = $this->global->GetUserData('password', array(), TCMSUserInput::FILTER_PASSWORD);
        $oUser = new TCMSUser();
        $translator = $this->getTranslator();

        if (PREVENT_PARALLEL_LOGINS_FOR_NON_ADMINS && $this->IsUserAlreadyLoggedIn($username)) {
            $this->data['errmsg'] = $translator->trans('chameleon_system_core.cms_module_login.error_user_already_logged_in');
            $this->data['username'] = $username;
        } else {
            if ($oUser->Login($username, $password)) {
                $this->redirectOnPendingUpdates();

                $bIsRefreshLogin = ($username == $sLastLoggedInUser);
                $this->postLoginRedirect($bIsRefreshLogin);
            } else {
                sleep(5);
                if ('' === $this->getCurrentHashedUserPassword($username)) {
                    $this->data['errmsg'] = $translator->trans('chameleon_system_core.cms_module_login.error_reset_login_data');
                } else {
                    $this->data['errmsg'] = $translator->trans('chameleon_system_core.cms_module_login.error_invalid_login_data');
                }
                $this->data['username'] = $username;
            }
        }
    }

    /**
     * @param string $username
     *
     * @return string
     * @psalm-suppress FalsableReturnStatement
     */
    private function getCurrentHashedUserPassword($username)
    {
        return $this->getDatabaseConnection()->fetchColumn('SELECT `crypted_pw` FROM `cms_user` WHERE `login` = :login LIMIT 1', array(
            'login' => $username,
        ));
    }

    /**
     * @param bool $bIsRefreshLogin
     *
     * @return void
     */
    protected function postLoginRedirect($bIsRefreshLogin)
    {
        $aRedirectParams = [];

        if ($bIsRefreshLogin) {
            $sRedirectURL = $_SERVER['HTTP_REFERER'];
            $aParams = TTools::GetURLArguments($sRedirectURL);
            if (array_key_exists('redirectParams', $aParams)) {
                $sParamString = $aParams['redirectParams'];
                $aRedirectParamsTmp = explode('&', $sParamString);
                if (count($aRedirectParamsTmp) > 0) {
                    $aRedirectParams = array();
                    foreach ($aRedirectParamsTmp as $sUrlPart) {
                        $aParts = explode('=', $sUrlPart);
                        if (2 == count($aParts)) {
                            $aRedirectParams[$aParts[0]] = $aParts[1];
                        } else {
                            $aRedirectParams[$aParts[0]] = '';
                        }
                    }
                }
            }
        }

        /**
         * @psalm-suppress UndefinedInterfaceMethod
         * @FIXME The `HeaderRedirect` method only exists on 1 of the 2 interface implementations.
         */
        $this->controller->HeaderRedirect($aRedirectParams);
    }

    /**
     * checks if open locks for the username are present
     * the admin account isn`t checked!
     *
     * @param string $sUsername
     *
     * @return bool
     */
    protected function IsUserAlreadyLoggedIn($sUsername)
    {
        $bOpenLocksFound = false;
        if ('admin' != $sUsername) {
            $sUserQuery = "SELECT * FROM `cms_user` WHERE `login` = '".MySqlLegacySupport::getInstance()->real_escape_string($sUsername)."'";
            $userResult = MySqlLegacySupport::getInstance()->query($sUserQuery);

            if (1 == MySqlLegacySupport::getInstance()->num_rows($userResult)) {
                $aUserRow = MySqlLegacySupport::getInstance()->fetch_assoc($userResult);
                $sQuery = "SELECT * FROM `cms_lock` WHERE `cms_user_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($aUserRow['id'])."' AND TIMESTAMPDIFF(MINUTE,`time_stamp`,CURRENT_TIMESTAMP()) <= ".RECORD_LOCK_TIMEOUT;
                $result = MySqlLegacySupport::getInstance()->query($sQuery);
                if (MySqlLegacySupport::getInstance()->num_rows($result) > 0) {
                    $bOpenLocksFound = true;
                }
            }
        }

        return $bOpenLocksFound;
    }

    /**
     * @param bool $noRedirect
     * @return void
     */
    public function Logout($noRedirect = false)
    {
        TCMSUser::Logout();

        if (!$noRedirect) {
            $this->getRedirect()->redirect(PATH_CMS_CONTROLLER.'?pagedef=login');
        }
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
