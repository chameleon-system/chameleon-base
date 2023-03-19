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
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
/**
 * manages class autoload. Details see Ticket #8793.
/**/
class AutoClassLoader
{
    /**
     * @var array<string, string>|false
     */
    private static $aClassRepository = false;

    private static array $runningNestedSearchForClass = [];

    private static string $autoClassesDir;

    /**
     * @param class-string $sClassName
     */
    public static function loadClassDefinition($sClassName): bool
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
                // try to autoload using other autoloader
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
            self::getLogger()->warning(sprintf("class %s not found in file system [%s]", $sClassName, $path));
        }

        return $bLoaded;
    }

    private static function getClassSubtypeFromCache(string $sClassName): ?string
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
     */
    private static function loadClassRepository(bool $bForceRegenerate = false): void
    {
        if (false !== self::$aClassRepository && false === $bForceRegenerate) {
            return;
        }
        self::$aClassRepository = false;
        self::$autoClassesDir = self::getAutoClassesDir();
        $sFullFile = self::$autoClassesDir.'/'.'autoloader.chameleon.txt';
        if (file_exists($sFullFile)) {
            self::$aClassRepository = include $sFullFile;
        }
        if (!is_array(self::$aClassRepository)) {
            self::$aClassRepository = false;
        }
    }

    private static function getPathToClass(string $className, string $subtype): string
    {
        return sprintf("%s/%s/%s.class.php", self::$autoClassesDir, $subtype, $className);
    }

    private static function getAutoClassesDir(): string
    {
        return ServiceLocator::getParameter('chameleon_system_autoclasses.cache_warmer.autoclasses_dir');
    }

    private static function getLogger(): LoggerInterface
    {
        return ServiceLocator::get('logger');
    }
}
