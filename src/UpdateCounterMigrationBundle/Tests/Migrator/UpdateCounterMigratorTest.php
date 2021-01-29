<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\UpdateCounterMigrationBundle\Tests\Migrator;

use ChameleonSystem\DatabaseMigration\DataModel\MigrationDataModel;
use ChameleonSystem\DatabaseMigration\Factory\MigrationDataModelFactoryInterface;
use ChameleonSystem\UpdateCounterMigrationBundle\DataAccess\CounterMigrationDataAccessInterface;
use ChameleonSystem\UpdateCounterMigrationBundle\Migrator\UpdateCounterMigrator;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use TCMSConfig;

class UpdateCounterMigratorTest extends TestCase
{
    use ProphecyTrait;

    private $mapping = null;

    /**
     * @var MigrationDataModelFactoryInterface
     */
    private $fileSystemModels = null;

    /**
     * @var CounterMigrationDataAccessInterface
     */
    private $dataAccess = null;
    /**
     * @var TCMSConfig
     */
    private $config;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mapping = null;
        $this->config = $this->prophesize('\TCMSConfig');
        $this->fileSystemModels = $this->prophesize('\ChameleonSystem\DatabaseMigration\Factory\MigrationDataModelFactoryInterface');
        $this->dataAccess = $this->prophesize('\ChameleonSystem\UpdateCounterMigrationBundle\DataAccess\CounterMigrationDataAccessInterface');
        $this->dataAccess->copyCounter(\Prophecy\Argument::any(), \Prophecy\Argument::any())->willReturn();
        $this->dataAccess->addUpdatesToCounter(\Prophecy\Argument::any(), \Prophecy\Argument::any())->willReturn();
    }

    /**
     * @test
     */
    public function it_copies_the_old_counter_to_the_new_one()
    {
        $this->givenAMapping(array('foo' => 'bar'));
        $this->givenNoUpdateFiles();
        $this->givenAnExistingCounter('foo');
        $this->givenNoExistingCounter('bar');
        $this->whenMigrating();
        $this->thenTheCounterShouldBeCopiedTo('foo', 'bar');
    }

    /**
     * @test
     */
    public function it_fills_the_old_counter_the_new_files()
    {
        $this->givenAMapping(array('foo' => 'bar'));
        $this->givenUpdateFiles(array('bar' => array('1' => '', '2' => '', '3' => '')));
        $this->givenAnExistingCounter('foo');
        $this->givenAnExistingCounter('bar');
        $this->whenMigrating();
        $this->thenTheUpdatesShouldBeAddedToTheCounter(array('1', '2', '3'), 'foo');
    }

    /**
     * This test should be explained in a bit more detail:.
     *
     * When we configure a mapping, we map db systemnames to db systemnames
     *
     * On the file system, new updates will be found with the ordinary update key representing folder names
     *
     * If we find new local file system updates in the new counter, we want them to be added to the old counter as well
     * So we need to be able to see if the new updates map to the old systemname
     *
     * We map "dbversion-meta-customer-hotfixbundle-up" to "dbversion-meta-packages-HotFixBundle/hotfixbundle-updates"
     * The new update files appear in HotFixBundle/hotfixbundle-updates
     * So we need to add those to the entry with the systemname "dbversion-meta-customer-hotfixbundle-up"
     *
     * This isn't very intuitive, but it is how the update system works, so this test is very important to understand and pass.
     *
     * @test
     */
    public function it_can_map_systemnames_to_model_types()
    {
        $this->givenAMapping(array('dbversion-meta-customer-hotfixbundle-up' => 'dbversion-meta-packages-HotFixBundle/hotfixbundle-updates'));
        $this->givenUpdateFiles(array('HotFixBundle/hotfixbundle-updates' => array('1' => '', '2' => '', '3' => '')));
        $this->givenAnExistingCounter('dbversion-meta-customer-hotfixbundle-up');
        $this->givenAnExistingCounter('dbversion-meta-packages-HotFixBundle/hotfixbundle-updates');
        $this->whenMigrating();
        $this->thenTheUpdatesShouldBeAddedToTheCounter(array('1', '2', '3'), 'dbversion-meta-customer-hotfixbundle-up');
    }

    private function givenAMapping($mapping)
    {
        $this->mapping = $mapping;
    }

    private function givenAnExistingCounter($counter)
    {
        $this->dataAccess->counterExists($counter)->willReturn(true);
    }

    private function givenNoExistingCounter($string)
    {
        $this->dataAccess->counterExists($string)->willReturn(false);
    }

    private function givenNoUpdateFiles()
    {
        $this->fileSystemModels->createMigrationDataModels()->willReturn(array());
    }

    private function givenUpdateFiles($updateFiles)
    {
        $models = array();
        foreach ($updateFiles as $bundleName => $files) {
            $models[] = new MigrationDataModel($bundleName, $files);
        }
        $this->fileSystemModels->createMigrationDataModels()->willReturn($models);
    }

    private function whenMigrating()
    {
        $migrator = new UpdateCounterMigrator($this->mapping, $this->fileSystemModels->reveal(), $this->dataAccess->reveal(), $this->config->reveal());
        $migrator->migrate();
    }

    private function thenTheCounterShouldBeCopiedTo($from, $to)
    {
        $this->dataAccess->copyCounter($from, $to)->shouldHaveBeenCalled();
    }

    private function thenTheUpdatesShouldBeAddedToTheCounter($updates, $counter)
    {
        $this->dataAccess->addUpdatesToCounter($updates, $counter)->shouldHaveBeenCalled();
    }
}
