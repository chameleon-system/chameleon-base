<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\AutoclassesBundle\CacheWarmer;

use ChameleonSystem\AutoclassesBundle\ClassManager\AutoclassesMapGeneratorInterface;
use ChameleonSystem\AutoclassesBundle\ClassManager\AutoclassesManagerInterface;
use IPkgCmsFileManager;

class AutoclassesCacheWarmer
{
    /**
     * @var AutoclassesDatabaseAdapterInterface
     */
    private $databaseAdapter;
    /**
     * @var AutoclassesManagerInterface
     */
    private $autoClassManager;
    /**
     * @var AutoclassesMapGeneratorInterface
     */
    private $autoclassesMapGenerator;
    /**
     * @var IPkgCmsFileManager
     */
    private $fileManager;
    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @param AutoclassesManagerInterface         $autoClassManager
     * @param AutoclassesDatabaseAdapterInterface $databaseAdapter
     * @param AutoclassesMapGeneratorInterface    $autoclassesMapGenerator
     * @param IPkgCmsFileManager                  $filemanager
     * @param string                              $cacheDir
     */
    public function __construct(AutoclassesManagerInterface $autoClassManager, AutoclassesDatabaseAdapterInterface $databaseAdapter, AutoclassesMapGeneratorInterface $autoclassesMapGenerator, IPkgCmsFileManager $filemanager, $cacheDir)
    {
        $this->autoClassManager = $autoClassManager;
        $this->databaseAdapter = $databaseAdapter;
        $this->autoclassesMapGenerator = $autoclassesMapGenerator;
        $this->fileManager = $filemanager;
        $this->cacheDir = $cacheDir;
    }

    /**
     * @param string $id
     *
     * @return void
     */
    public function updateTableById($id)
    {
        $tablename = $this->databaseAdapter->getTableNameForId($id);
        if (null !== $tablename) {
            $this->updateTableByName($tablename);
        }
    }

    /**
     * @param string $tablename
     *
     * @return void
     */
    public function updateTableByName($tablename)
    {
        $classesToCreate = $this->getClassListForTableName($tablename);
        foreach ($classesToCreate as $classToCreate) {
            $this->autoClassManager->create($classToCreate, $this->cacheDir);
        }
        $this->regenerateClassmap($this->cacheDir);
    }

    /**
     * @return void
     */
    public function updateAllTables()
    {
        $targetDir = $this->cacheDir;
        $autoclassesExistedBefore = false;
        if (file_exists($targetDir)) {
            $targetDir = $this->createTempCacheDir();
            $autoclassesExistedBefore = true;
        }
        $tableClasses = $this->getTableClassNamesToLoad();
        foreach (array('virtualClasses', 'tableClasses') as $type) {
            foreach ($tableClasses[$type] as $class) {
                $this->autoClassManager->create($class, $targetDir);
            }
            $this->regenerateClassmap($targetDir);
        }

        if ($autoclassesExistedBefore) {
            $this->makeTempDirToAutoclassesDir($targetDir);
        }
    }

    /**
     * @return string
     */
    private function createTempCacheDir()
    {
        $tempCacheDir = $this->cacheDir;
        $tempCacheDir = rtrim($tempCacheDir, DIRECTORY_SEPARATOR);
        $tempCacheDir[strlen($tempCacheDir) - 1] = '_';
        $tempCacheDir .= DIRECTORY_SEPARATOR;

        if (file_exists($tempCacheDir)) {
            $this->fileManager->deldir($tempCacheDir, true);
        }
        mkdir($tempCacheDir, 0777, true);

        return $tempCacheDir;
    }

    /**
     * @param string $targetDir
     *
     * @return void
     */
    private function makeTempDirToAutoclassesDir($targetDir)
    {
        $oldCacheDir = $this->getOldCacheDir();
        if (file_exists($oldCacheDir)) {
            $this->fileManager->deldir($oldCacheDir, true);
        }
        $this->fileManager->move($this->cacheDir, $oldCacheDir);
        $this->fileManager->move($targetDir, $this->cacheDir);
        $this->fileManager->deldir($oldCacheDir, true);
    }

    /**
     * @return string
     */
    private function getOldCacheDir()
    {
        $dir = $this->cacheDir;
        $dir = rtrim($dir, DIRECTORY_SEPARATOR);
        $dir .= '_old';
        $dir .= DIRECTORY_SEPARATOR;

        return $dir;
    }

    /**
     * @return array
     */
    public function getTableClassNamesToLoad()
    {
        // get all table classes
        $result = $this->databaseAdapter->getTableClassList();

        $convertedList = array();
        foreach ($result as $bareClassName) {
            $classNames = $this->getClassListForTableName($bareClassName);
            $convertedList = array_merge($convertedList, $classNames);
        }

        $result2 = $this->databaseAdapter->getVirtualClassList();

        $convertedList = array('virtualClasses' => $result2, 'tableClasses' => $convertedList);

        return $convertedList;
    }

    /**
     * @param string $bareClassName
     *
     * @return string
     */
    private function convertToCamelCase($bareClassName)
    {
        $className = preg_replace_callback('/(_.)/', function ($matches) {
            return strtoupper(substr($matches[0], 1, 1));
        }, $bareClassName);

        return strtoupper(substr($className, 0, 1)).substr($className, 1);
    }

    /**
     * @param string $bareClassName
     *
     * @return string[]
     */
    private function getClassListForTableName($bareClassName)
    {
        $list = array();
        $realClassName = $this->convertToCamelCase($bareClassName);
        $list[] = 'Tdb'.$realClassName;
        $list[] = 'TAdb'.$realClassName;
        $list[] = 'Tdb'.$realClassName.'List';
        $list[] = 'TAdb'.$realClassName.'List';

        return $list;
    }

    /**
     * @param string|null $targetDir
     *
     * @return string
     */
    public function regenerateClassmap($targetDir = null)
    {
        if (null === $targetDir) {
            $targetDir = $this->cacheDir;
        }
        $classData = $this->autoclassesMapGenerator->generateAutoclassesMap($targetDir);
        $filePath = $targetDir.'autoloader.chameleon.txt';
        $file = $this->fileManager->fopen($filePath, 'wb');
        $this->writeClassmap($file, $classData);
        $this->fileManager->fclose($file);

        return $filePath;
    }

    /**
     * @param resource $file
     * @param array    $classData
     *
     * @return void
     */
    private function writeClassmap($file, array $classData)
    {
        $this->fileManager->fwrite($file, '<?php return array(');
        foreach ($classData as $class => $path) {
            $this->fileManager->fwrite($file, "'$class'=>'$path',");
        }
        $this->fileManager->fwrite($file, ');');
    }
}
