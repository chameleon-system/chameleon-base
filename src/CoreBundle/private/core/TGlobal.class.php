<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\BackendBreadcrumbService;
use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use Symfony\Component\Translation\TranslatorInterface;

class TGlobal extends TGlobalBase
{
    /**
     * url history management.
     *
     * @var TCMSURLHistory
     *
     * @deprecated since 6.0.14 - use service BackendBreadCrumbService::getBreadcrumb() instead.
     */
    protected $oURLHistory = null;

    const MODE_BACKEND = 0;
    const MODE_FRONTEND = 1;
    private static $mode = self::MODE_FRONTEND;

    public static function setMode($mode)
    {
        self::$mode = $mode;
    }

    public function __get($sParameterName)
    {
        if (self::MODE_FRONTEND === self::$mode) {
            if ('oUser' === $sParameterName) {
                $this->oUser = &TCMSUser::GetActiveUser();
                if (!$this->oUser && 'true' == !$this->GetUserData('__modulechooser')) {
                    // no user set yet... so autologin the webuser...
                    $this->oUser = new TCMSUser();
                    $aUserData = $this->GetWebuserLoginData();
                    $this->oUser->Login($aUserData['loginName'], $aUserData['password']);
                }

                return $this->oUser;
            } else {
                return parent::__get($sParameterName);
            }
        } else {
            if ('oUser' === $sParameterName) {
                return TCMSUser::GetActiveUser();
            } elseif ('oURLHistory' === $sParameterName) {
                return $this->getBreadcrumbService()->getBreadcrumb();
            } else {
                trigger_error('ERROR - parameter requested from TGlobal that does not exist', E_USER_ERROR);
            }
        }
    }

    /**
     * call it to find out if we are in the cms or on the webpage.
     *
     * @return bool
     */
    public static function IsCMSMode()
    {
        return self::MODE_FRONTEND !== self::$mode;
    }

    public function GetWebuserLoginData()
    {
        return array('loginName' => 'www', 'password' => 'www');
    }

    /**
     * Translates the given message.
     *
     * @param string      $id         The message id (may also be an object that can be cast to string)
     * @param array       $parameters An array of parameters for the message
     * @param string|null $domain     The domain for the message or null to use the default
     * @param string|null $locale     The locale or null to use the default
     *
     * @throws InvalidArgumentException If the locale contains invalid characters
     *
     * @return string The translated string
     *
     * @deprecated deprecated since 6.1.0 - please use the "translator" service
     */
    public static function Translate($id, $parameters = array(), $domain = null, $locale = null)
    {
        return self::getTranslator()->trans($id, is_array($parameters) ? $parameters : array(), $domain, $locale);
    }

    /**
     * return the current active language (language is loaded from page , url prefix  or user -depending on mode)
     * If in template engine get language from active edit language.
     *
     * @param string $sSetActiveLanguageId - sets the active language so we can use language simulation
     *
     * @return string
     *
     * @deprecated since 6.0.0 - use \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.language_service')->getActiveLanguageId() instead
     */
    public static function GetActiveLanguageId($sSetActiveLanguageId = null)
    {
        /** @var LanguageServiceInterface $languageService */
        $languageService = ServiceLocator::get('chameleon_system_core.language_service');

        return $languageService->getActiveLanguageId();
    }

    /**
     * returns the value of variable $name or if missing the whole array filtered by $excludeArray
     * the data is unfiltered.
     *
     * @param string $name
     * @param array  $excludeArray
     * @param string $sFilterClass - form: classname;path;type|classname;path;type
     *
     * @return mixed - string or array
     *
     * @deprecated since 6.2.0 - use InputFilterUtilInterface::getFiltered*Input() instead.
     */
    public function GetUserData($name = null, $excludeArray = array(), $sFilterClass = TCMSUSERINPUT_DEFAULTFILTER)
    {
        if (self::MODE_BACKEND === self::$mode) {
            $sFilterClass = '';
        }

        return parent::GetUserData($name, $excludeArray, $sFilterClass);
    }

    /**
     * returns a list of all language ids the current user is allowed to edit.
     *
     * @return array
     */
    public function GetLanguageIdList()
    {
        if (self::MODE_BACKEND === self::$mode) {
            if (null === $this->aLangaugeIds) {
                $databaseConnection = ServiceLocator::get('database_connection');
                $this->aLangaugeIds = array($this->oUser->sqlData['cms_language_id']);
                // fetch user language list
                $tmp = explode(',', $this->oUser->sqlData['languages']);
                foreach ($tmp as $lang) {
                    $query = 'SELECT * FROM `cms_language` WHERE `iso_6391` = :languageCode';
                    if ($langrow = $databaseConnection->fetchAssoc($query, array('languageCode' => trim($lang)))) {
                        $this->aLangaugeIds[] = $langrow['id'];
                    }
                }
                if (count($this->aLangaugeIds) < 1) {
                    $oCMSConfig = TdbCmsConfig::GetInstance();
                    $this->aLangaugeIds[] = $oCMSConfig->fieldTranslationBaseLanguageId;
                }
            }

            return $this->aLangaugeIds;
        }

        return parent::GetLanguageIdList();
    }

    /**
     * @deprecated since 6.3.0 - use service BackendBreadCrumbService::getBreadcrumb() instead.
     *
     * @return TCMSURLHistory|null - returns null if not in backend.
     */
    public function &GetURLHistory()
    {
        return $this->getBreadcrumbService()->getBreadcrumb();
    }

    /**
     * @return TranslatorInterface
     */
    private static function getTranslator()
    {
        return ServiceLocator::get('translator');
    }

    private function getBreadcrumbService(): BackendBreadcrumbService
    {
        return ServiceLocator::get('chameleon_system_core.service.backend_breadcrumb');
    }
}
