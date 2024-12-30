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

use ChameleonSystem\AutoclassesBundle\ClassManager\AutoclassesManagerInterface;
use ChameleonSystem\AutoclassesBundle\ClassManager\AutoclassesMapGeneratorInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

class AutoclassesCacheWarmer implements CacheWarmerInterface
{
    private AutoclassesDatabaseAdapterInterface $databaseAdapter;
    private AutoclassesManagerInterface $autoClassManager;
    private AutoclassesMapGeneratorInterface $autoclassesMapGenerator;
    private Filesystem $fileManager;
    private string $cacheDir;

    public function __construct(
        AutoclassesManagerInterface $autoClassManager,
        AutoclassesDatabaseAdapterInterface $databaseAdapter,
        AutoclassesMapGeneratorInterface $autoclassesMapGenerator,
        Filesystem $filemanager,
        string $cacheDir,
        ContainerInterface $container
    ) {
        $this->autoClassManager = $autoClassManager;
        $this->databaseAdapter = $databaseAdapter;
        $this->autoclassesMapGenerator = $autoclassesMapGenerator;
        $this->fileManager = $filemanager;
        $this->cacheDir = $cacheDir;
        ServiceLocator::setContainer($container);
    }

    public function warmUp(string $cacheDirectory): array
    {
        $this->updateAllTables();

        return [];
    }

    public function isOptional(): bool
    {
        return false;
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
        foreach (['virtualClasses', 'tableClasses'] as $type) {
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
            $this->fileManager->remove($tempCacheDir);
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
            $this->fileManager->remove($oldCacheDir);
        }
        $this->fileManager->rename($this->cacheDir, $oldCacheDir, true);
        $this->fileManager->rename($targetDir, $this->cacheDir, true);
        $this->fileManager->remove($oldCacheDir);
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

        $convertedList = [];
        foreach ($result as $bareClassName) {
            $classNames = $this->getClassListForTableName($bareClassName);
            $convertedList = array_merge($convertedList, $classNames);
        }

        $result2 = $this->databaseAdapter->getVirtualClassList();

        $convertedList = ['virtualClasses' => $result2, 'tableClasses' => $convertedList];

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
        $list = [];
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
        $file = fopen($filePath, 'wb');
        $this->writeClassmap($file, $classData);
        fclose($file);

        return $filePath;
    }

    /**
     * @param resource $file
     *
     * @return void
     */
    private function writeClassmap($file, array $classData)
    {
        fwrite($file, '<?php return array(');
        foreach ($classData as $class => $path) {
            fwrite($file, "'$class'=>'$path',");
        }
        fwrite($file, ');');
    }
}
