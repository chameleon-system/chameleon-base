<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\DatabaseMigration\Tests\Factory;

use ChameleonSystem\DatabaseMigration\DataModel\MigrationDataModel;
use ChameleonSystem\DatabaseMigration\Factory\FileSystemMigrationDataModelFactory;
use ChameleonSystem\DatabaseMigration\Tests\Factory\fixtures\TestBundle\TestBundle;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class FileSystemMigrationDataModelFactoryTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function itProducesTheCorrectModels()
    {
        $testBundle = new TestBundle();
        $migrationPathUtilMock = $this->prophesize('\ChameleonSystem\DatabaseMigration\Util\MigrationPathUtilInterface');

        $migrationPathUtilMock->getUpdateFoldersFromBundlePath($testBundle->getPath())->willReturn([
            __DIR__.'/fixtures/TestBundle/Bridge/Chameleon/Migration/Script/alternative-updates',
            __DIR__.'/fixtures/TestBundle/Bridge/Chameleon/Migration/Script/updates',
        ]);

        $migrationPathUtilMock->getUpdateFilesFromFolder(__DIR__.'/fixtures/TestBundle/Bridge/Chameleon/Migration/Script/alternative-updates')->willReturn([
            __DIR__.'/fixtures/TestBundle/Bridge/Chameleon/Migration/Script/alternative-updates/update-7.inc.php',
            __DIR__.'/fixtures/TestBundle/Bridge/Chameleon/Migration/Script/alternative-updates/update-8.inc.php',
        ]);
        $migrationPathUtilMock->getUpdateFilesFromFolder(__DIR__.'/fixtures/TestBundle/Bridge/Chameleon/Migration/Script/updates')->willReturn([
            __DIR__.'/fixtures/TestBundle/Bridge/Chameleon/Migration/Script/updates/update-1.inc.php',
            __DIR__.'/fixtures/TestBundle/Bridge/Chameleon/Migration/Script/updates/update-2.inc.php',
        ]);

        $migrationPathUtilMock->getBuildNumberFromUpdateFile(__DIR__.'/fixtures/TestBundle/Bridge/Chameleon/Migration/Script/alternative-updates/update-7.inc.php')->willReturn(7);
        $migrationPathUtilMock->getBuildNumberFromUpdateFile(__DIR__.'/fixtures/TestBundle/Bridge/Chameleon/Migration/Script/alternative-updates/update-8.inc.php')->willReturn(8);
        $migrationPathUtilMock->getBuildNumberFromUpdateFile(__DIR__.'/fixtures/TestBundle/Bridge/Chameleon/Migration/Script/updates/update-1.inc.php')->willReturn(1);
        $migrationPathUtilMock->getBuildNumberFromUpdateFile(__DIR__.'/fixtures/TestBundle/Bridge/Chameleon/Migration/Script/updates/update-2.inc.php')->willReturn(2);

        $kernelMock = $this->prophesize('\Symfony\Component\HttpKernel\KernelInterface');
        $kernelMock->getBundles()->willReturn([
            $testBundle,
        ]);
        $factory = new FileSystemMigrationDataModelFactory($migrationPathUtilMock->reveal(), $kernelMock->reveal());

        $result = $factory->createMigrationDataModels();

        static::assertEquals([
            'TestBundle' => new MigrationDataModel('TestBundle', [
                1 => __DIR__.'/fixtures/TestBundle/Bridge/Chameleon/Migration/Script/updates/update-1.inc.php',
                2 => __DIR__.'/fixtures/TestBundle/Bridge/Chameleon/Migration/Script/updates/update-2.inc.php',
                7 => __DIR__.'/fixtures/TestBundle/Bridge/Chameleon/Migration/Script/alternative-updates/update-7.inc.php',
                8 => __DIR__.'/fixtures/TestBundle/Bridge/Chameleon/Migration/Script/alternative-updates/update-8.inc.php',
            ]),
        ], $result);
    }
}
