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

    protected function setUp()
    {
        parent::setUp();
        $this->util = new MigrationPathUtil();
        $this->util->addPathToUpdatesInBundle('/Bridge\/Chameleon\/Migration\/Script\/.*updates/');
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->util = null;
    }

    /**
     * @test
     */
    public function it_gets_all_update_paths_from_bundle_folder()
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
    public function it_does_not_crash_when_unconfigured()
    {
        $util = new MigrationPathUtil();
        $folders = $util->getUpdateFoldersFromBundlePath(__DIR__.'/fixtures/TestBundle');
        $this->assertEmpty($folders);
    }

    /**
     * @test
     */
    public function it_gets_all_update_paths_from_bundle_folder_with_multiple_bases()
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
    public function it_works_with_alternative_patterns()
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
    public function it_handles_non_existent_paths()
    {
        $bundleFolder = __DIR__.'/fixtures/TestBundleNonExistent';
        $expected = array();

        $this->assertEquals($expected, $this->util->getUpdateFoldersFromBundlePath($bundleFolder));
    }

    /**
     * @test
     */
    public function it_gets_all_update_files_from_folder()
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
    public function it_does_not_crash_on_non_existent_folders()
    {
        $result = $this->util->getUpdateFilesFromFolder(__DIR__.'/fixtures/TestBundle/I/do/not/exist');

        $this->assertCount(0, $result);
    }

    public function buildNumberDataProvider()
    {
        return array(
            array('update-12345.inc.php', '12345'),
            array('update-0.inc.php', '0'),
            array('/with/a/path/update-12345.inc.php', '12345'),
            array('12345.inc.php', '12345'),
        );
    }

    /**
     * @test
     * @dataProvider buildNumberDataProvider
     */
    public function it_gets_the_build_number_from_file($file, $expectedBuildNumber)
    {
        $this->assertEquals($expectedBuildNumber, $this->util->getBuildNumberFromUpdateFile($file));
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage File does not contain a build number
     */
    public function it_throws_an_exception_if_no_build_number_can_be_extracted()
    {
        $this->util->getBuildNumberFromUpdateFile('foobar');
    }
}
