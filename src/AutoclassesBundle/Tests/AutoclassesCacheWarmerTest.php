<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\AutoclassesBundle\Tests;

use ChameleonSystem\AutoclassesBundle\CacheWarmer\AutoclassesCacheWarmer;
use ChameleonSystem\AutoclassesBundle\CacheWarmer\AutoclassesDatabaseAdapterInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class AutoclassesCacheWarmerTest extends TestCase
{
    use ProphecyTrait;

    /** @var AutoclassesCacheWarmer */
    private $warmer;

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->warmer = null;
    }

    /**
     * @test
     */
    public function it_updates_single_tables_by_id()
    {
        /** @var $manager \ChameleonSystem\AutoclassesBundle\ClassManager\AutoclassesManagerInterface|ObjectProphecy */
        $manager = $this->prophesize('\ChameleonSystem\AutoclassesBundle\ClassManager\AutoclassesManagerInterface');
        /** @var $adapter AutoclassesDatabaseAdapterInterface|ObjectProphecy */
        $adapter = $this->prophesize('\ChameleonSystem\AutoclassesBundle\CacheWarmer\AutoclassesDatabaseAdapterInterface');
        $adapter
            ->getTableNameForId('123')
            ->willReturn(
                'foo_table'
            )
        ;
        /** @var \IPkgCmsFileManager|ObjectProphecy $filemanager */
        $filemanager = $this->prophesize('\IPkgCmsFileManager');
        $cacheDir = __DIR__.'/cache/';
        /** @var $autoclassesMapGenerator \ChameleonSystem\AutoclassesBundle\ClassManager\AutoclassesMapGeneratorInterface|ObjectProphecy */
        $autoclassesMapGenerator = $this->prophesize('\ChameleonSystem\AutoclassesBundle\ClassManager\AutoclassesMapGeneratorInterface');
        $autoclassesMapGenerator
            ->generateAutoclassesMap($cacheDir)
            ->willReturn(array(
                'TestClass' => 'TestType',
            ));

        $this->warmer = new AutoclassesCacheWarmer($manager->reveal(), $adapter->reveal(), $autoclassesMapGenerator->reveal(), $filemanager->reveal(), $cacheDir);

        $this->warmer->updateTableById('123');

        $manager->create('TdbFooTable', $cacheDir)->shouldHaveBeenCalled();
        $manager->create('TdbFooTableList', $cacheDir)->shouldHaveBeenCalled();
        $manager->create('TAdbFooTable', $cacheDir)->shouldHaveBeenCalled();
        $manager->create('TAdbFooTableList', $cacheDir)->shouldHaveBeenCalled();
    }

    /**
     * @test
     */
    public function it_updates_single_tables_by_name()
    {
        /** @var $manager \ChameleonSystem\AutoclassesBundle\ClassManager\AutoclassesManagerInterface|ObjectProphecy */
        $manager = $this->prophesize('\ChameleonSystem\AutoclassesBundle\ClassManager\AutoclassesManagerInterface');
        /** @var $adapter AutoclassesDatabaseAdapterInterface|ObjectProphecy */
        $adapter = $this->prophesize('\ChameleonSystem\AutoclassesBundle\CacheWarmer\AutoclassesDatabaseAdapterInterface');
        /** @var \IPkgCmsFileManager|ObjectProphecy $filemanager */
        $filemanager = $this->prophesize('\IPkgCmsFileManager');

        $cacheDir = __DIR__.'/cache/';

        /** @var $autoclassesMapGenerator \ChameleonSystem\AutoclassesBundle\ClassManager\AutoclassesMapGeneratorInterface|ObjectProphecy */
        $autoclassesMapGenerator = $this->prophesize('\ChameleonSystem\AutoclassesBundle\ClassManager\AutoclassesMapGeneratorInterface');
        $autoclassesMapGenerator
            ->generateAutoclassesMap($cacheDir)
            ->willReturn(array(
                'TestClass' => 'TestType',
            ));

        $this->warmer = new AutoclassesCacheWarmer($manager->reveal(), $adapter->reveal(), $autoclassesMapGenerator->reveal(), $filemanager->reveal(), $cacheDir);

        $this->warmer->updateTableByName('foo_table');

        $manager->create('TdbFooTable', $cacheDir)->shouldHaveBeenCalled();
        $manager->create('TdbFooTableList', $cacheDir)->shouldHaveBeenCalled();
        $manager->create('TAdbFooTable', $cacheDir)->shouldHaveBeenCalled();
        $manager->create('TAdbFooTableList', $cacheDir)->shouldHaveBeenCalled();
    }

    /**
     * @test
     */
    public function it_ignores_nonexistant_tables()
    {
        /** @var $adapter AutoclassesDatabaseAdapterInterface|ObjectProphecy */
        $adapter = $this->prophesize('\ChameleonSystem\AutoclassesBundle\CacheWarmer\AutoclassesDatabaseAdapterInterface');
        $adapter
            ->getTableNameForId('123')
            ->willReturn(
                null
            );
        /** @var $manager \ChameleonSystem\AutoclassesBundle\ClassManager\AutoclassesManagerInterface|ObjectProphecy */
        $manager = $this->prophesize('\ChameleonSystem\AutoclassesBundle\ClassManager\AutoclassesManagerInterface');
        /** @var $autoclassesMapGenerator \ChameleonSystem\AutoclassesBundle\ClassManager\AutoclassesMapGeneratorInterface|ObjectProphecy */
        $autoclassesMapGenerator = $this->prophesize('\ChameleonSystem\AutoclassesBundle\ClassManager\AutoclassesMapGeneratorInterface');
        /** @var \IPkgCmsFileManager|ObjectProphecy $filemanager */
        $filemanager = $this->prophesize('\IPkgCmsFileManager');
        $cacheDir = '';

        $this->warmer = new AutoclassesCacheWarmer($manager->reveal(), $adapter->reveal(), $autoclassesMapGenerator->reveal(), $filemanager->reveal(), $cacheDir);

        $this->warmer->updateTableById('123');

        $manager->create(Argument::any(), $cacheDir)->shouldNotHaveBeenCalled();
    }

    /**
     * @test
     */
    public function it_converts_underscore_names_to_autoclass_names()
    {
        /** @var $manager \ChameleonSystem\AutoclassesBundle\ClassManager\AutoclassesManagerInterface|ObjectProphecy */
        $manager = $this->prophesize('\ChameleonSystem\AutoclassesBundle\ClassManager\AutoclassesManagerInterface');
        /** @var $adapter AutoclassesDatabaseAdapterInterface|ObjectProphecy */
        $adapter = $this->prophesize('\ChameleonSystem\AutoclassesBundle\CacheWarmer\AutoclassesDatabaseAdapterInterface');
        $adapter
            ->getTableClassList()
            ->willReturn(
                array(
                    'foo_bar',
                    'bar_baz',
                    'a_b_cx',
                )
            );
        $adapter
            ->getVirtualClassList()
            ->willReturn(
                array(
                    'vfoo_bar',
                    'vbar_baz',
                    'va_b_cx',
                )
            );
        /** @var $autoclassesMapGenerator \ChameleonSystem\AutoclassesBundle\ClassManager\AutoclassesMapGeneratorInterface|ObjectProphecy */
        $autoclassesMapGenerator = $this->prophesize('\ChameleonSystem\AutoclassesBundle\ClassManager\AutoclassesMapGeneratorInterface');
        /** @var \IPkgCmsFileManager|ObjectProphecy $filemanager */
        $filemanager = $this->prophesize('\IPkgCmsFileManager');
        $cacheDir = '';

        $expected = array(
            'virtualClasses' => array(
                'vfoo_bar',
                'vbar_baz',
                'va_b_cx',
            ),
            'tableClasses' => array(
                'TdbFooBar',
                'TAdbFooBar',
                'TdbFooBarList',
                'TAdbFooBarList',
                'TdbBarBaz',
                'TAdbBarBaz',
                'TdbBarBazList',
                'TAdbBarBazList',
                'TdbABCx',
                'TAdbABCx',
                'TdbABCxList',
                'TAdbABCxList',
            ),
        );

        $this->warmer = new AutoclassesCacheWarmer($manager->reveal(), $adapter->reveal(), $autoclassesMapGenerator->reveal(), $filemanager->reveal(), $cacheDir);

        $result = $this->warmer->getTableClassNamesToLoad();
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function it_should_warm_the_complete_cache()
    {
        /** @var $adapter AutoclassesDatabaseAdapterInterface|ObjectProphecy */
        $adapter = $this->prophesize('\ChameleonSystem\AutoclassesBundle\CacheWarmer\AutoclassesDatabaseAdapterInterface');
        $adapter
            ->getTableClassList()
            ->willReturn(
                array(
                    'foo_bar',
                )
            );
        $adapter
            ->getVirtualClassList()
            ->willReturn(
                array(
                    'vfoo_bar',
                )
            );
        /** @var \IPkgCmsFileManager|ObjectProphecy $filemanager */
        $filemanager = $this->prophesize('\IPkgCmsFileManager');
        $cacheDir = __DIR__.'/cache/';
        $tempCacheDir = __DIR__.'/cach_/';
        /** @var $autoclassesMapGenerator \ChameleonSystem\AutoclassesBundle\ClassManager\AutoclassesMapGeneratorInterface|ObjectProphecy */
        $autoclassesMapGenerator = $this->prophesize('\ChameleonSystem\AutoclassesBundle\ClassManager\AutoclassesMapGeneratorInterface');
        $autoclassesMapGenerator
            ->generateAutoclassesMap($tempCacheDir)
            ->willReturn(array(
                'TestClass' => 'TestType',
            ));

        /** @var $manager \ChameleonSystem\AutoclassesBundle\ClassManager\AutoclassesManagerInterface|ObjectProphecy */
        $manager = $this->prophesize('\ChameleonSystem\AutoclassesBundle\ClassManager\AutoclassesManagerInterface');

        $this->warmer = new AutoclassesCacheWarmer($manager->reveal(), $adapter->reveal(), $autoclassesMapGenerator->reveal(), $filemanager->reveal(), $cacheDir);

        $this->warmer->updateAllTables();

        $manager->create('TdbFooBar', $tempCacheDir)->shouldHaveBeenCalled();
        $manager->create('TAdbFooBar', $tempCacheDir)->shouldHaveBeenCalled();
        $manager->create('TdbFooBarList', $tempCacheDir)->shouldHaveBeenCalled();
        $manager->create('TAdbFooBarList', $tempCacheDir)->shouldHaveBeenCalled();
        $manager->create('vfoo_bar', $tempCacheDir)->shouldHaveBeenCalled();

        rmdir($tempCacheDir);
    }
}
