<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\DatabaseMigration\Tests\Util;

use ChameleonSystem\DatabaseMigration\Util\MigrationPathUtil;
use PHPUnit\Framework\TestCase;

class MigrationPathUtilTest extends TestCase
{
    /**
     * @var MigrationPathUtil
     */
    private $util;

    protected function setUp(): void
    {
        parent::setUp();
        $this->util = new MigrationPathUtil();
        $this->util->addPathToUpdatesInBundle('/Bridge\/Chameleon\/Migration\/Script\/.*updates/');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->util = null;
    }

    /**
     * @test
     */
    public function itGetsAllUpdatePathsFromBundleFolder()
    {
        $bundleFolder = __DIR__.'/fixtures/TestBundle';
        $result = $this->util->getUpdateFoldersFromBundlePath($bundleFolder);
        $this->assertContains(__DIR__.'/fixtures/TestBundle/Bridge/Chameleon/Migration/Script/alternative-updates', $result);
        $this->assertContains(__DIR__.'/fixtures/TestBundle/Bridge/Chameleon/Migration/Script/updates', $result);
        $this->assertCount(2, $result);
    }

    /**
     * @test
     */
    public function itDoesNotCrashWhenUnconfigured()
    {
        $util = new MigrationPathUtil();
        $folders = $util->getUpdateFoldersFromBundlePath(__DIR__.'/fixtures/TestBundle');
        $this->assertEmpty($folders);
    }

    /**
     * @test
     */
    public function itGetsAllUpdatePathsFromBundleFolderWithMultipleBases()
    {
        $bundleFolder = __DIR__.'/fixtures/TestBundle';
        $this->util = new MigrationPathUtil();

        $this->util->addPathToUpdatesInBundle('/Bridge\/Chameleon\/Migration\/Script\/.*updates/');
        $this->util->addPathToUpdatesInBundle('/^[^\/]*updates/');
        $result = $this->util->getUpdateFoldersFromBundlePath($bundleFolder);
        $this->assertContains(__DIR__.'/fixtures/TestBundle/Bridge/Chameleon/Migration/Script/alternative-updates', $result);
        $this->assertContains(__DIR__.'/fixtures/TestBundle/Bridge/Chameleon/Migration/Script/updates', $result);
        $this->assertContains(__DIR__.'/fixtures/TestBundle/test-updates', $result);
        $this->assertCount(3, $result);
    }

    /**
     * @test
     */
    public function itWorksWithAlternativePatterns()
    {
        $bundleFolder = __DIR__.'/fixtures/TestBundle';
        $this->util = new MigrationPathUtil();
        $this->util->addPathToUpdatesInBundle('/^[^\/]*builds/');
        $result = $this->util->getUpdateFoldersFromBundlePath($bundleFolder);
        $this->assertContains(__DIR__.'/fixtures/TestBundle/dbbuilds', $result);
        $this->assertCount(1, $result);
    }

    /**
     * @test
     */
    public function itHandlesNonExistentPaths()
    {
        $bundleFolder = __DIR__.'/fixtures/TestBundleNonExistent';
        $expected = [];

        $this->assertEquals($expected, $this->util->getUpdateFoldersFromBundlePath($bundleFolder));
    }

    /**
     * @test
     */
    public function itGetsAllUpdateFilesFromFolder()
    {
        $result = $this->util->getUpdateFilesFromFolder(__DIR__.'/fixtures/TestBundle/Bridge/Chameleon/Migration/Script/alternative-updates');

        $this->assertContains(__DIR__.'/fixtures/TestBundle/Bridge/Chameleon/Migration/Script/alternative-updates/update-1.inc.php', $result);
        $this->assertContains(__DIR__.'/fixtures/TestBundle/Bridge/Chameleon/Migration/Script/alternative-updates/update-2.inc.php', $result);
        $this->assertNotContains(__DIR__.'/fixtures/TestBundle/Bridge/Chameleon/Migration/Script/alternative-updates/update.inc.php', $result);
        $this->assertCount(2, $result);
    }

    /**
     * @test
     */
    public function itDoesNotCrashOnNonExistentFolders()
    {
        $result = $this->util->getUpdateFilesFromFolder(__DIR__.'/fixtures/TestBundle/I/do/not/exist');

        $this->assertCount(0, $result);
    }

    public function buildNumberDataProvider()
    {
        return [
            ['update-12345.inc.php', '12345'],
            ['update-0.inc.php', '0'],
            ['/with/a/path/update-12345.inc.php', '12345'],
            ['12345.inc.php', '12345'],
        ];
    }

    /**
     * @test
     *
     * @dataProvider buildNumberDataProvider
     */
    public function itGetsTheBuildNumberFromFile($file, $expectedBuildNumber)
    {
        $this->assertEquals($expectedBuildNumber, $this->util->getBuildNumberFromUpdateFile($file));
    }

    /**
     * @test
     */
    public function itThrowsAnExceptionIfNoBuildNumberCanBeExtracted()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('File does not contain a build number');

        $this->util->getBuildNumberFromUpdateFile('foobar');
    }
}
