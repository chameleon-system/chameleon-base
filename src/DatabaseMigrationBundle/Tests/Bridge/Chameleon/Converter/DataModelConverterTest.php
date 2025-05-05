<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\DatabaseMigrationBundle\Bridge\Chameleon\Converter;

use ChameleonSystem\DatabaseMigration\DataModel\MigrationDataModel;
use PHPUnit\Framework\TestCase;

function realpath($path)
{
    return $path;
}

class DataModelConverterTest extends TestCase
{
    private $modelMap;
    private $target;
    private $output;

    protected function setUp(): void
    {
        parent::setUp();
        $this->output = null;
        $this->modelMap = null;
        $this->output = null;
    }

    /**
     * @test
     */
    public function itConvertsModelsToStdClasses()
    {
        $this->givenAMapOfModels();
        $this->givenATargetStructure();
        $this->whenConvertIsCalled();
        $this->thenTheOutputShouldBeEqualToTheTarget();
    }

    private function whenConvertIsCalled()
    {
        $converter = new DataModelConverter('/foo/bar');
        $this->output = $converter->convertDataModelsToLegacySystem($this->modelMap);
    }

    private function thenTheOutputShouldBeEqualToTheTarget()
    {
        $this->assertEquals($this->target, $this->output);
    }

    private function givenAMapOfModels()
    {
        $map = [];

        $map['ChameleonSystemFooBundle'] = new MigrationDataModel('ChameleonSystemFooBundle');
        $map['ChameleonSystemFooBundle']->addFile('1', '/foo/bar/baz/updateFile1.inc.php');
        $map['ChameleonSystemFooBundle']->addFile('2', '/foo/bar/baz/updateFile2.inc.php');

        $this->modelMap = $map;
    }

    private function givenATargetStructure()
    {
        $target = [];
        $target['ChameleonSystemFooBundle'] = [];

        $class = new \stdClass();
        $class->fileName = 'baz/updateFile1.inc.php';
        $class->buildNumber = '1';
        $class->bundleName = 'ChameleonSystemFooBundle';
        $target['ChameleonSystemFooBundle'][] = $class;
        $class = new \stdClass();
        $class->fileName = 'baz/updateFile2.inc.php';
        $class->buildNumber = '2';
        $class->bundleName = 'ChameleonSystemFooBundle';
        $target['ChameleonSystemFooBundle'][] = $class;

        $this->target = $target;
    }
}
