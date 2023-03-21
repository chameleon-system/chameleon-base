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
use ChameleonSystem\AutoclassesBundle\Exception\TPkgCmsCoreAutoClassManagerException_Recursion;
use IPkgCmsFileManager;

class AutoclassesCacheWarmer
{
    private AutoclassesDatabaseAdapterInterface $databaseAdapter;
    private AutoclassesManagerInterface $autoClassManager;
    private AutoclassesMapGeneratorInterface $autoclassesMapGenerator;
    private IPkgCmsFileManager $fileManager;
    private string $cacheDir;

    public function __construct(AutoclassesManagerInterface $autoClassManager, AutoclassesDatabaseAdapterInterface $databaseAdapter, AutoclassesMapGeneratorInterface $autoclassesMapGenerator, IPkgCmsFileManager $filemanager, string $cacheDir)
    {
        $this->autoClassManager = $autoClassManager;
        $this->databaseAdapter = $databaseAdapter;
        $this->autoclassesMapGenerator = $autoclassesMapGenerator;
        $this->fileManager = $filemanager;
        $this->cacheDir = $cacheDir;
    }

    public function updateTableById(string $id): void
    {
        $tableName = $this->databaseAdapter->getTableNameForId($id);

        if (null !== $tableName) {
            $this->updateTableByName($tableName);
        }
    }

    public function updateTableByName(string $tableName): void
    {
        $classesToCreate = $this->getClassListForTableName($tableName);
        foreach ($classesToCreate as $classToCreate) {
            $this->autoClassManager->create($classToCreate, $this->cacheDir);
        }
        $this->regenerateClassmap($this->cacheDir);
    }

    /**
     * @throws TPkgCmsCoreAutoClassManagerException_Recursion
     */
    public function updateAllTables(): void
    {
        $targetDir = $this->cacheDir;
        $autoClassesExistedBefore = file_exists($targetDir);

        if (true === $autoClassesExistedBefore) {
            $targetDir = $this->createTempCacheDir();
        }

        $tableClassNames = $this->getTableClassNamesToLoad();

        foreach (['virtualClasses', 'tableClasses'] as $type) {
            foreach ($tableClassNames[$type] as $className) {
                $this->autoClassManager->create($className, $targetDir);
            }
            $this->regenerateClassmap($targetDir);
        }

        if (true === $autoClassesExistedBefore) {
            $this->makeTempDirToAutoClassesDir($targetDir);
        }
    }

    private function createTempCacheDir(): string
    {
        $tempCacheDir = rtrim($this->cacheDir, DIRECTORY_SEPARATOR) . '_' . DIRECTORY_SEPARATOR;

        if (true === file_exists($tempCacheDir)) {
            $this->fileManager->deldir($tempCacheDir, true);
        }

        mkdir($tempCacheDir, 0777, true);

        return $tempCacheDir;
    }

    private function makeTempDirToAutoClassesDir(string $targetDir): void
    {
        $oldCacheDir = $this->getOldCacheDir();
        if (true === file_exists($oldCacheDir)) {
            $this->fileManager->deldir($oldCacheDir, true);
        }
        $this->fileManager->move($this->cacheDir, $oldCacheDir);
        $this->fileManager->move($targetDir, $this->cacheDir);
        $this->fileManager->deldir($oldCacheDir, true);
    }


    private function getOldCacheDir(): string
    {
        return rtrim($this->cacheDir, DIRECTORY_SEPARATOR) . '_old' . DIRECTORY_SEPARATOR;;
    }

    public function getTableClassNamesToLoad(): array
    {
        // get all table classes
        $result = $this->databaseAdapter->getTableClassList();

        $convertedList = [];
        foreach ($result as $bareClassName) {
            $classNames = $this->getClassListForTableName($bareClassName);
            $convertedList = array_merge($convertedList, $classNames);
        }

        $virtualClassList = $this->databaseAdapter->getVirtualClassList();

        return ['virtualClasses' => $virtualClassList, 'tableClasses' => $convertedList];
    }

    private function convertToCamelCase(string $bareClassName): string
    {
        $className = preg_replace_callback('/(_.)/', function ($matches) {
            return strtoupper(substr($matches[0], 1, 1));
        }, $bareClassName);

        return strtoupper(substr($className, 0, 1)) . substr($className, 1);
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
        $list[] = 'Tdb' . $realClassName;
        $list[] = 'TAdb' . $realClassName;
        $list[] = 'Tdb' . $realClassName . 'List';
        $list[] = 'TAdb' . $realClassName . 'List';

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
        $filePath = $targetDir . 'autoloader.chameleon.txt';
        $file = $this->fileManager->fopen($filePath, 'wb');
        $this->writeClassmap($file, $classData);
        $this->fileManager->fclose($file);

        return $filePath;
    }

    /**
     * @param resource $file
     * @param array $classData
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
