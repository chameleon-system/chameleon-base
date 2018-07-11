<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * singleton registry class.
 *
 * @deprecated since 6.2.0 - mutable global state singleton. Nope, nope, nope.
/**/
class TCMSRegistry
{
    public static $aRegistry = array();

    /**
     * set a variable in the registry.
     *
     * @param string $name
     * @param var    $value
     */
    public static function Set($name, $value)
    {
        self::$aRegistry[$name] = $value;
    }

    /**
     * returns contents of registry.
     *
     * @param string $name - variable name
     *
     * @return var - false if var does not exist
     */
    public static function Get($name)
    {
        if (array_key_exists($name, self::$aRegistry)) {
            return self::$aRegistry[$name];
        } else {
            return null;
        }
    }

    /**
     * returns array of var keys.
     *
     * @return array
     */
    public static function GetVarNames()
    {
        return array_keys(self::$aRegistry);
    }
}
