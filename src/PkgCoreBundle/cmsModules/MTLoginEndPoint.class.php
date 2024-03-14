<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CmsStringUtilitiesBundle\Interfaces\UrlUtilityServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\UpdateCounterMigrationBundle\Exception\InvalidMigrationCounterException;
use Doctrine\DBAL\Connection;
use Symfony\Contracts\Translation\TranslatorInterface;

class MTLoginEndPoint extends TCMSModelBase
{
    public function &Execute()
    {
        $this->data = parent::Execute();

        $this->data['username'] = $this->global->GetUserData('username');

        $this->data['redirectParams'] = '';
        if ($this->global->UserDataExists('redirectParams')) {
            $this->data['redirectParams'] = $this->global->GetUserData('redirectParams');
        } 

        if ($this->global->CMSUserDefined()) {
            $this->getRedirect()->redirectToActivePage([]);
        }

        return $this->data;
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
        $password = $this->global->GetUserData('password', array(), TCMSUserInput::FILTER_PASSWORD);
        $oUser = new TCMSUser();
        $translator = $this->getTranslator();

        if (PREVENT_PARALLEL_LOGINS_FOR_NON_ADMINS && $this->IsUserAlreadyLoggedIn($username)) {
            $this->data['errmsg'] = $translator->trans('chameleon_system_core.cms_module_login.error_user_already_logged_in');
            $this->data['username'] = $username;
        } else {
            if ($oUser->Login($username, $password)) {
                $this->redirectOnPendingUpdates();

                $this->postLoginRedirect();
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
     * @return string
     * @psalm-suppress FalsableReturnStatement
     */
    private function getCurrentHashedUserPassword(string $username)
    {
        return $this->getDatabaseConnection()->fetchColumn('SELECT `crypted_pw` FROM `cms_user` WHERE `login` = :login LIMIT 1', array(
            'login' => $username,
        ));
    }

    protected function postLoginRedirect(): void
    {
        $redirectParams = [];
        
        $inputFilter = $this->getInputFilterUtilService();
        $redirectParamsEncoded = $inputFilter->getFilteredInput('redirectParams', '');
        if ('' === $redirectParamsEncoded) {
            return;
        }

        $urlParams = urldecode($redirectParamsEncoded);
        parse_str($urlParams, $redirectParams);
    
        $this->getRedirect()->redirectToActivePage($this->filterRedirectParameter($redirectParams));
    }
    
    protected function filterRedirectParameter(array $redirectParams): array
    {
        // Prevent redirecting to a page that was loaded in an iframe.
        if (array_key_exists('bIsLoadedFromIFrame', $redirectParams)) {
            return [];
        }
        
        $validRedirectParams = $this->getValidRedirectParameter();

        return array_intersect_key($redirectParams, array_flip($validRedirectParams));
    }
    
    protected function getValidRedirectParameter(): array
    {
        return [
            'pagedef',
            '_pagedefType',
            'id',
            '_rmhist',
            '_histid',
            'tableid'
        ];
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

        $redirectParamsEncoded = $this->getInputFilterUtilService()->getFilteredGetInput('redirectParams');
        
        if (!$noRedirect) {
            $url = PATH_CMS_CONTROLLER.'?pagedef=login';
            if (null !== $redirectParamsEncoded) {
                $url.= '&redirectParams='.urlencode($redirectParamsEncoded);
            }
            $this->getRedirect()->redirect($url);
        }
    }

    private function redirectOnPendingUpdates(): void
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

    private function getDatabaseConnection(): Connection
    {
        return ServiceLocator::get('database_connection');
    }

    private function getRedirect(): ICmsCoreRedirect
    {
        return ServiceLocator::get('chameleon_system_core.redirect');
    }

    private function getTranslator(): TranslatorInterface
    {
        return ServiceLocator::get('translator');
    }
    
    private function getInputFilterUtilService(): InputFilterUtilInterface
    {
        return ServiceLocator::get('chameleon_system_core.util.input_filter');
    }
}
