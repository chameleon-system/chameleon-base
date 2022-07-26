<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\UpdateCounterMigrationBundle\Migrator;

use ChameleonSystem\DatabaseMigration\DataModel\MigrationDataModel;
use ChameleonSystem\DatabaseMigration\Factory\MigrationDataModelFactoryInterface;
use ChameleonSystem\UpdateCounterMigrationBundle\DataAccess\CounterMigrationDataAccessInterface;
use ChameleonSystem\UpdateCounterMigrationBundle\Exception\CounterMigrationException;

class UpdateCounterMigrator
{
    /**
     * @var array
     */
    private $mapping;
    /**
     * @var MigrationDataModelFactoryInterface
     */
    private $fileSystemModels;
    /**
     * @var CounterMigrationDataAccessInterface
     */
    private $dataAccess;

    /**
     * @param array                               $mapping
     * @param MigrationDataModelFactoryInterface  $fileSystemModels
     * @param CounterMigrationDataAccessInterface $dataAccess
     */
    public function __construct(array $mapping, MigrationDataModelFactoryInterface $fileSystemModels, CounterMigrationDataAccessInterface $dataAccess)
    {
        $this->mapping = $mapping;
        $this->fileSystemModels = $fileSystemModels;
        $this->dataAccess = $dataAccess;
    }

    /**
     * @throws CounterMigrationException
     *
     * @return void
     */
    public function migrate()
    {
        if (0 === \count($this->mapping)) {
            return;
        }

        $fileSystemMigrationModels = $this->fileSystemModels->createMigrationDataModels();
        foreach ($this->mapping as $source => $target) {
            $this->dataAccess->copyCounter($source, $target);
            $this->fillOldCounterWithNewData($fileSystemMigrationModels, $source, $target);
        }
    }

    /**
     * @param MigrationDataModel[] $fileSystemMigrationModels
     * @param string               $oldCounter                - database counter systemname
     * @param string               $newCounter                - database counter systemname
     *
     * @throws CounterMigrationException
     *
     * @return void
     */
    private function fillOldCounterWithNewData(array $fileSystemMigrationModels, $oldCounter, $newCounter)
    {
        $modelTypeForNewCounter = $this->getModelTypeForNewCounter($newCounter);
        foreach ($fileSystemMigrationModels as $migrationModel) {
            if ($modelTypeForNewCounter === $migrationModel->getBundleName()) {
                $this->dataAccess->addUpdatesToCounter(array_keys($migrationModel->getBuildNumberToFileMap()), $oldCounter);
            }
        }
    }

    /**
     * The new counter specified in the mapping is the db column `systemname`, the type we need is the model type. so we need to convert here!
     * For a more elaborated explanation, please see the test that will pass if this code does its job in \ChameleonSystem\UpdateCounterMigrationBundle\Tests\UpdateCounterMigratorTest::it_can_map_systemnames_to_model_types.
     *
     * @param string $newCounter
     *
     * @return string
     */
    private function getModelTypeForNewCounter($newCounter)
    {
        $type = $newCounter;
        $prefix = 'dbversion-meta-packages-';
        if (0 === strpos($newCounter, $prefix)) {
            $type = substr($newCounter, strlen($prefix));
        }

        return $type;
    }
}
