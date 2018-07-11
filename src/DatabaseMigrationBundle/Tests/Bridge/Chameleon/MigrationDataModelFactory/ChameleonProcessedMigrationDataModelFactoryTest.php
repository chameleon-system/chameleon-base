<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\DatabaseMigration\DataModel\MigrationDataModel;
use ChameleonSystem\DatabaseMigrationBundle\Bridge\Chameleon\MigrationDataModelFactory\ChameleonProcessedMigrationDataModelFactory;
use PHPUnit\Framework\TestCase;

class ChameleonProcessedMigrationDataModelFactoryTest extends TestCase
{
    /**
     * @test
     * @dataProvider getDatabaseAccessResultAndMigrationModels
     *
     * @param array                $alreadyProcessedUpdates
     * @param MigrationDataModel[] $expectedMigrationModels
     */
    public function it_creates_migration_data_models($alreadyProcessedUpdates, array $expectedMigrationModels)
    {
        $migrationManagerMock = $this->createMock('\ChameleonSystem\DatabaseMigration\DataAccess\MigrationDataAccessInterface');
        $migrationManagerMock->expects($this->any())->method('getProcessedMigrationData')->will($this->returnValue($alreadyProcessedUpdates));
        $factory = new ChameleonProcessedMigrationDataModelFactory($migrationManagerMock);
        $createdMigrationModels = $factory->createMigrationDataModels();
        $this->assertEquals($expectedMigrationModels, $createdMigrationModels);
    }

    /**
     * @return array
     */
    public function getDatabaseAccessResultAndMigrationModels()
    {
        return array(
            array(
                // processed updates
                array(
                    ['bundle_name' => 'ChameleonSystemCmsClassManagerBundle', 'build_number' => 1],
                    ['bundle_name' => 'ChameleonSystemCmsClassManagerBundle', 'build_number' => 2],
                    ['bundle_name' => 'EsonoCustomerBundle', 'build_number' => 1],
                    ['bundle_name' => 'EsonoCustomerBundle', 'build_number' => 2],
                ),
                // expected migration models
                array(
                    'ChameleonSystemCmsClassManagerBundle' => new MigrationDataModel('ChameleonSystemCmsClassManagerBundle', array('1' => '', '2' => '')),
                    'EsonoCustomerBundle' => new MigrationDataModel('EsonoCustomerBundle', array('1' => '', '2' => '')),
                ),
            ),
        );
    }
}
