<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\DatabaseMigration\Tests\Model;

use ChameleonSystem\DatabaseMigration\DataModel\MigrationDataModel;
use PHPUnit\Framework\TestCase;

class MigrationDataModelTest extends TestCase
{
    public const TESTBUNDLE = 'ChameleonSystemFooBundle';

    /**
     * @var MigrationDataModel
     */
    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new MigrationDataModel(self::TESTBUNDLE);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->model = null;
    }

    /**
     * @test
     */
    public function itReturnsTheCorrectType()
    {
        $this->assertEquals(self::TESTBUNDLE, $this->model->getBundleName());
    }

    /**
     * @test
     */
    public function itReturnsAnEmptyResult()
    {
        $this->assertEquals([], $this->model->getBuildNumberToFileMap());
    }

    /**
     * @test
     */
    public function itAddsBuildFiles()
    {
        $this->model->addFile('1', 'foo/bar');
        $this->model->addFile('1', 'foo/baz');
        $this->model->addFile('2', 'foo/foobar');

        $expected = [
            '1' => 'foo/bar',
            '2' => 'foo/foobar',
        ];
        $this->assertEquals($expected, $this->model->getBuildNumberToFileMap());
    }
}
