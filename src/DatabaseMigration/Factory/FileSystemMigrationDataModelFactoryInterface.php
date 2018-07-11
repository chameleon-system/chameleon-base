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

/**
 * Interface MigrationsDataModelFactoryInterface.
 *
 * The MigrationsDataModelFactory is a bridge between the DatabaseMigrationBundle and the Symfony kernel.
 * It is used to pull data for the bundle into the respective models based on information usually gathered from the Symfony kernel instance
 */
interface FileSystemMigrationDataModelFactoryInterface extends MigrationDataModelFactoryInterface
{
    /**
     * @return MigrationDataModel[]
     */
    public function createMigrationDataModels();
}
