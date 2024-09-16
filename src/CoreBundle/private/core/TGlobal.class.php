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
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use Symfony\Contracts\Translation\TranslatorInterface;

class TGlobal extends TGlobalBase
{
    const MODE_BACKEND = 0;
    const MODE_FRONTEND = 1;

    private static $mode = self::MODE_FRONTEND;

    public static function setMode($mode)
    {
        self::$mode = $mode;
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
     * @deprecated 7.2 fetch the edit languages from SecurityHelperAccess::getUser()?->getAvailableEditLanguages()
     */
    public function GetLanguageIdList()
    {
        if (self::MODE_BACKEND === self::$mode) {
            // get security helper
            /** @var SecurityHelperAccess $securityHelper */
            $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
            $languages = $securityHelper->getUser()?->getAvailableEditLanguages();
            if (null === $languages) {
               $languages = [];
            } else {
                $languages = array_keys($languages);
            }
            if (0 === count($languages)) {
                $languages = [TdbCmsConfig::GetInstance()->fieldTranslationBaseLanguageId];
            }
            return $languages;
        }

        return parent::GetLanguageIdList();
    }

    private static function getTranslator(): TranslatorInterface
    {
        return ServiceLocator::get('translator');
    }
}
