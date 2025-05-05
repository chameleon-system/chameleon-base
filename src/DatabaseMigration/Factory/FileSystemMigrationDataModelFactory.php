<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\DatabaseMigration\Factory;

use ChameleonSystem\DatabaseMigration\DataModel\MigrationDataModel;
use ChameleonSystem\DatabaseMigration\Util\MigrationPathUtilInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class FileSystemMigrationDataModelFactory implements FileSystemMigrationDataModelFactoryInterface
{
    /**
     * @var MigrationPathUtilInterface
     */
    private $migrationPathUtil;
    /**
     * @var KernelInterface
     */
    private $kernel;

    public function __construct(MigrationPathUtilInterface $migrationPathUtil, KernelInterface $kernel)
    {
        $this->migrationPathUtil = $migrationPathUtil;
        $this->kernel = $kernel;
    }

    /**
     * {@inheritdoc}
     */
    public function createMigrationDataModels()
    {
        $models = [];
        $bundles = $this->kernel->getBundles();
        foreach ($bundles as $bundle) {
            $updateDirectories = $this->migrationPathUtil->getUpdateFoldersFromBundlePath($bundle->getPath());
            foreach ($updateDirectories as $updateDirectory) {
                $models = $this->addModelsForDirectory($models, $bundle->getName(), $updateDirectory);
            }
        }

        return $models;
    }

    /**
     * @param string $bundleName
     * @param string $updateDirectory
     */
    private function addModelsForDirectory(array $models, $bundleName, $updateDirectory): array
    {
        $updateFiles = $this->migrationPathUtil->getUpdateFilesFromFolder($updateDirectory);

        if (isset($models[$bundleName])) {
            $model = $models[$bundleName];
        } else {
            $model = new MigrationDataModel($bundleName);
        }
        foreach ($updateFiles as $updateFile) {
            $buildNumber = $this->migrationPathUtil->getBuildNumberFromUpdateFile($updateFile);
            $model->addFile($buildNumber, $updateFile);
        }
        $models[$bundleName] = $model;

        return $models;
    }
}
