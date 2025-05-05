<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\DatabaseMigrationBundle\Bridge\Chameleon\MigrationDataModelFactory;

use ChameleonSystem\DatabaseMigration\DataAccess\MigrationDataAccessInterface;
use ChameleonSystem\DatabaseMigration\DataModel\MigrationDataModel;
use ChameleonSystem\DatabaseMigration\Factory\MigrationDataModelFactoryInterface;

class ChameleonProcessedMigrationDataModelFactory implements MigrationDataModelFactoryInterface
{
    /**
     * @var MigrationDataAccessInterface
     */
    private $migrationDataAccess;

    public function __construct(MigrationDataAccessInterface $migrationDataAccess)
    {
        $this->migrationDataAccess = $migrationDataAccess;
    }

    /**
     * {@inheritdoc}
     */
    public function createMigrationDataModels()
    {
        /**
         * @var MigrationDataModel[] $models
         */
        $models = [];

        foreach ($this->migrationDataAccess->getProcessedMigrationData() as $row) {
            $bundleName = $row['bundle_name'];
            $buildNumber = $row['build_number'];
            if (false === array_key_exists($bundleName, $models)) {
                $models[$bundleName] = new MigrationDataModel($bundleName);
            }
            $model = $models[$bundleName];
            $model->addFile($buildNumber, '');
        }

        return $models;
    }
}
