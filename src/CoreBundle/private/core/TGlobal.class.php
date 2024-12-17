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
use Symfony\Contracts\Translation\TranslatorInterface;

class TGlobal extends TGlobalBase
{
    public const MODE_BACKEND = 0;
    public const MODE_FRONTEND = 1;

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
        return ['loginName' => 'www', 'password' => 'www'];
    }

    /**
     * Translates the given message.
     *
     * @param string $id The message id (may also be an object that can be cast to string)
     * @param array $parameters An array of parameters for the message
     * @param string|null $domain The domain for the message or null to use the default
     * @param string|null $locale The locale or null to use the default
     *
     * @return string The translated string
     *
     * @throws InvalidArgumentException If the locale contains invalid characters
     *
     * @deprecated deprecated since 6.1.0 - please use the "translator" service
     */
    public static function Translate($id, $parameters = [], $domain = null, $locale = null)
    {
        return self::getTranslator()->trans($id, is_array($parameters) ? $parameters : [], $domain, $locale);
    }

    /**
     * returns the value of variable $name or if missing the whole array filtered by $excludeArray
     * the data is unfiltered.
     *
     * @param string $name
     * @param array $excludeArray
     * @param string $sFilterClass - form: classname;path;type|classname;path;type
     *
     * @return mixed - string or array
     *
     * @deprecated since 6.2.0 - use InputFilterUtilInterface::getFiltered*Input() instead.
     */
    public function GetUserData($name = null, $excludeArray = [], $sFilterClass = TCMSUSERINPUT_DEFAULTFILTER)
    {
        if (self::MODE_BACKEND === self::$mode) {
            $sFilterClass = '';
        }

        return parent::GetUserData($name, $excludeArray, $sFilterClass);
    }

    private static function getTranslator(): TranslatorInterface
    {
        return ServiceLocator::get('translator');
    }
}
