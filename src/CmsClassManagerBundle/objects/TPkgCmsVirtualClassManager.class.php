<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\AutoclassesBundle\CacheWarmer\AutoclassesCacheWarmer;
use Doctrine\DBAL\Connection;

class TPkgCmsVirtualClassManager
{
    /**
     * @var null|array<string, mixed>
     */
    private $config = null;

    /**
     * @var null|string
     */
    private $entryPoint = null;
    /**
     * @var Connection
     */
    private $databaseConnection;

    /**
     * @param string $entryPointClass
     *
     * @return bool
     */
    public function load($entryPointClass)
    {
        $this->entryPoint = $entryPointClass;
        $config = $this->getConfig();

        return false !== $config && null !== $config;
    }

    /**
     * @param class-string $sClassName
     * @param string $sClassSubType
     * @param string $sClassType
     * @param bool $bRefresh
     * @return false|class-string
     */
    public static function GetEntryPointClassForClass($sClassName, $sClassSubType, $sClassType, $bRefresh = false)
    {
        static $aEntryPoints = null;
        if ($bRefresh) {
            $aEntryPoints = null;
        }
        $databaseConnection = \ChameleonSystem\CoreBundle\ServiceLocator::get('database_connection');
        $sEntryPointClass = false;
        if (is_null($aEntryPoints)) {
            $aEntryPoints = array();
            $query = 'SELECT `pkg_cms_class_manager`.`name_of_entry_point`
                  FROM `pkg_cms_class_manager`
               ';
            $tRes = $databaseConnection->query($query);
            while ($aRow = $tRes->fetch(\PDO::FETCH_ASSOC)) {
                if (!array_key_exists($aRow['name_of_entry_point'], $aEntryPoints)) {
                    $aEntryPoints[$aRow['name_of_entry_point']] = '1';
                }
            }
        }
        if (!$sEntryPointClass && array_key_exists($sClassName, $aEntryPoints)) {
            $sEntryPointClass = $sClassName;
        }

        return $sEntryPointClass;
    }

    /**
     * @param string|null $targetDir
     *
     * @return void
     */
    public function UpdateVirtualClasses($targetDir = null)
    {
        $aExtensionList = $this->getExtensionList();
        if ('' === $this->getConfigValue('name_of_entry_point') || '' === $this->getConfigValue('exit_class')) {
            return;
        }

        $sEntryPointExtensionName = '';
        if ('' !== $this->getConfigValue('exit_class')) {
            $sEntryPointExtensionName = $this->getConfigValue('exit_class');
        }

        $aPrevious = null;
        $bFirst = true;
        $aClasses = array();
        foreach ($aExtensionList as $extension) {
            $sAutoClassName = $this->getAutoParentClassFromClass($extension['class']);
            if ($bFirst) {
                $bFirst = false;
                if ('' !== $this->getConfigValue('exit_class')) {
                    $aClasses[$sAutoClassName] = "<?php\nclass ".$sAutoClassName." extends {$this->getConfigValue('exit_class')} {}";
                } else {
                    $aClasses[$sAutoClassName] = "<?php\nclass ".$sAutoClassName.' {}';
                }
            } else {
                $aClasses[$sAutoClassName] = "<?php\nclass ".$sAutoClassName." extends {$aPrevious['class']} {}";
            }
            $aPrevious = $extension;
            $sEntryPointExtensionName = $extension['class'];
        }
        $aClasses[$this->getConfigValue('name_of_entry_point')] = "<?php\nclass {$this->getConfigValue('name_of_entry_point')} extends {$sEntryPointExtensionName} {}";
        $path = $this->getAutoDataObjectPath($targetDir);
        $addedClasses = false;
        foreach ($aClasses as $className => $classContent) {
            $file = $path.$className.'.class.php';
            if (!$addedClasses && !file_exists($file)) {
                $addedClasses = true;
            }
            file_put_contents($file, $classContent);
        }
        if ($addedClasses) {
            $this->getAutoclassesCacheWarmer()->regenerateClassmap();
        }
    }

    /**
     * @param class-string $sClassName
     * @return string
     */
    private function getAutoParentClassFromClass($sClassName)
    {
        $autoParent = $sClassName.'AutoParent';
        if (false !== strpos($autoParent, '\\')) {
            $autoParent = str_replace('\\', '', $autoParent);
        }

        return $autoParent;
    }

    /**
     * update all virtual classes.
     *
     * @static
     *
     * @return void
     */
    public static function UpdateAllVirtualClasses()
    {
        $databaseConnection = \ChameleonSystem\CoreBundle\ServiceLocator::get('database_connection');

        $query = 'SELECT name_of_entry_point FROM pkg_cms_class_manager ORDER BY cmsident';
        $tRes = $databaseConnection->query($query);
        while ($entryPoint = $tRes->fetch(\PDO::FETCH_ASSOC)) {
            $virtualClassManager = new self();
            $virtualClassManager->setDatabaseConnection($databaseConnection);
            $virtualClassManager->load($entryPoint[0]);
            $virtualClassManager->UpdateVirtualClasses();
        }
    }

    /**
     * @return void
     */
    public function recreateAutoclasses()
    {
        $this->getAutoclassesCacheWarmer()->updateAllTables();
    }

    /**
     * @return false|string
     */
    protected function GetVirtualClassEntryPointFileName()
    {
        $sTargetFile = false;

        if ('' !== $this->getConfigValue('name_of_entry_point')) {
            $sTargetFile = $this->getAutoDataObjectPath().$this->getConfigValue('name_of_entry_point').'.class.php';
        }

        return $sTargetFile;
    }

    /**
     * @return null|array<string, mixed>
     */
    protected function getConfig()
    {
        if (null === $this->config || false === $this->config) {
            $query = 'SELECT `pkg_cms_class_manager`.*
                        FROM `pkg_cms_class_manager`
                       WHERE `pkg_cms_class_manager`.`name_of_entry_point` = :nameOfEntryPoint
                     ';

            try {
                $rRes = $this->getDatabaseConnection()->executeQuery($query, array('nameOfEntryPoint' => $this->entryPoint));
                $this->config = $rRes->fetch(\PDO::FETCH_ASSOC);
            } catch (\Doctrine\DBAL\DBALException $e) {
                $this->config = null;
            }
        }

        return $this->config;
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function getConfigValue($name)
    {
        $config = $this->getConfig();

        return (isset($config[$name])) ? $config[$name] : null;
    }

    /**
     * @return array
     */
    public function getExtensionList()
    {
        $aExtensionList = array();
        $query = 'SELECT *
                    FROM pkg_cms_class_manager_extension
                   WHERE pkg_cms_class_manager_id = :classManagerId
                ORDER BY `pkg_cms_class_manager_extension`.`position` ASC
                   ';
        $tRes = $this->getDatabaseConnection()->executeQuery($query, array('classManagerId' => $this->getConfigValue('id')));
        while ($extension = $tRes->fetch(\PDO::FETCH_ASSOC)) {
            $aExtensionList[] = $extension;
        }

        return $aExtensionList;
    }

    /**
     * @param string|null $basePath
     *
     * @return string
     */
    private function getAutoDataObjectPath($basePath = null)
    {
        if (null === $basePath) {
            $basePath = \ChameleonSystem\CoreBundle\ServiceLocator::getParameter('chameleon_system_autoclasses.cache_warmer.autoclasses_dir');
        }
        $path = $basePath.'CMSAutoDataObjects/';
        if (!file_exists($path)) {
            mkdir($path, 0777, true);

            return $path;
        }

        return $path;
    }

    /**
     * @param \Doctrine\DBAL\Connection $connection
     *
     * @return void
     */
    public function setDatabaseConnection(Connection $connection)
    {
        $this->databaseConnection = $connection;
    }

    /**
     * @return Connection
     */
    protected function getDatabaseConnection()
    {
        if (null !== $this->databaseConnection) {
            return $this->databaseConnection;
        }

        return \ChameleonSystem\CoreBundle\ServiceLocator::get('database_connection');
    }

    /**
     * @return AutoclassesCacheWarmer
     */
    private function getAutoclassesCacheWarmer()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_autoclasses.cache_warmer');
    }
}
