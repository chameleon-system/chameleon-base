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
    const TESTBUNDLE = 'ChameleonSystemFooBundle';

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
    public function it_returns_the_correct_type()
    {
        $this->assertEquals(self::TESTBUNDLE, $this->model->getBundleName());
    }

    /**
     * @test
     */
    public function it_returns_an_empty_result()
    {
        $this->assertEquals(array(), $this->model->getBuildNumberToFileMap());
    }

    /**
     * @test
     */
    public function it_adds_build_files()
    {
        $this->model->addFile('1', 'foo/bar');
        $this->model->addFile('1', 'foo/baz');
        $this->model->addFile('2', 'foo/foobar');

        $expected = array(
            '1' => 'foo/bar',
            '2' => 'foo/foobar',
        );
        $this->assertEquals($expected, $this->model->getBuildNumberToFileMap());
    }
}
