<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\AutoclassesBundle\Loader;

use ChameleonSystem\CoreBundle\ServiceLocator;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
 * manages class auto-loading. Details see Ticket #8793.
 * /**/
class AutoClassLoader
{
    /**
     * @var array<string, string>|false
     */
    private static $aClassRepository = false;
    /**
     * @var array
     */
    private static $runningNestedSearchForClass = [];
    /**
     * @var string we hold the autoclasses dir in this variable to avoid lots of container parameter lookups
     */
    private static $autoclassesDir;

    /**
     * @param class-string $sClassName
     *
     * @return bool
     */
    public static function loadClassDefinition($sClassName)
    {
        try {
            ServiceLocator::get('service_container');
        } catch (ServiceNotFoundException $e) {
            return false;
        }
        if (isset(self::$runningNestedSearchForClass[$sClassName])) {
            return false;
        }

        $classSubtype = self::getClassSubtypeFromCache($sClassName);
        if (null === $classSubtype) {
            self::loadClassRepository();
            $classSubtype = self::getClassSubtypeFromCache($sClassName);
            if (null === $classSubtype) {
                self::$runningNestedSearchForClass[$sClassName] = true;
                // try to auto load using other auto loader
                spl_autoload_call($sClassName);
                unset(self::$runningNestedSearchForClass[$sClassName]);
                if (class_exists($sClassName, false) || interface_exists($sClassName, false)) {
                    return true;
                }
                self::loadClassRepository(true);
                // last try
                $classSubtype = self::getClassSubtypeFromCache($sClassName);
                if (null === $classSubtype) {
                    return false;
                }
            }
        }

        $path = self::getPathToClass($sClassName, $classSubtype);

        $bLoaded = include_once $path; // we assume class files are safe
        if (false === $bLoaded) {
            trigger_error("class $sClassName not found in file system [$path]", E_USER_WARNING);
        }

        return $bLoaded;
    }

    /**
     * @param string $sClassName
     *
     * @return string|null
     */
    private static function getClassSubtypeFromCache($sClassName)
    {
        if (false === self::$aClassRepository) {
            return null;
        }
        if (false === isset(self::$aClassRepository[$sClassName])) {
            return null;
        }

        return self::$aClassRepository[$sClassName];
    }

    /**
     * fills class repository with a complete lookup of all classes.
     *
     * @param bool $bForceRegenerate
     *
     * @return void
     */
    private static function loadClassRepository($bForceRegenerate = false)
    {
        if (false !== self::$aClassRepository && false === $bForceRegenerate) {
            return;
        }
        self::$aClassRepository = false;
        self::$autoclassesDir = self::getAutoclassesDir();
        $sFullFile = self::$autoclassesDir.'/autoloader.chameleon.txt';
        if (file_exists($sFullFile)) {
            self::$aClassRepository = include $sFullFile;
        }
        if (!is_array(self::$aClassRepository)) {
            self::$aClassRepository = false;
        }
    }

    /**
     * @param string $className
     * @param string $subtype
     *
     * @return string
     */
    private static function getPathToClass($className, $subtype)
    {
        $autoclassesDir = self::$autoclassesDir;

        return "$autoclassesDir/$subtype/$className.class.php";
    }

    /**
     * @return string
     */
    private static function getAutoclassesDir()
    {
        return ServiceLocator::getParameter('chameleon_system_autoclasses.cache_warmer.autoclasses_dir');
    }
}
