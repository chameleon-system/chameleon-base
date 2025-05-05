<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// todo: add namespace
use ChameleonSystem\DatabaseMigration\DataModel\MigrationDataModel;
use ChameleonSystem\DatabaseMigration\Reducer\MigrationDataModelReducer;
use PHPUnit\Framework\TestCase;

class MigrationDataModelReducerTest extends TestCase
{
    /**
     * @var MigrationDataModel
     */
    private $dataModel;

    /**
     * @var MigrationDataModel
     */
    private $dataModelToReduceBy;

    /**
     * @var MigrationDataModel
     */
    private $reducedModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dataModel = null;
        $this->dataModelToReduceBy = null;
        $this->reducedModel = null;
    }

    public function getModels()
    {
        $originalModel1 = new MigrationDataModel('ChameleonSystemFooBundle');
        $originalModel1->addFile('1', 'file1');
        $originalModel1->addFile('2', 'file2');
        $originalModel1->addFile('3', 'file3');

        $reducerModel1 = new MigrationDataModel('ChameleonSystemFooBundle');
        $reducerModel1->addFile('1', '');
        $reducerModel1->addFile('2', '');

        $reducedModel1 = new MigrationDataModel('ChameleonSystemFooBundle');
        $reducedModel1->addFile('3', 'file3');

        return [
            [
                $originalModel1, $reducerModel1, $reducedModel1,
            ],
        ];
    }

    /**
     * @test
     *
     * @dataProvider getModels
     */
    public function itReducesAModel(MigrationDataModel $model, MigrationDataModel $modelToReduceBy, MigrationDataModel $expectedOutcome)
    {
        $this->givenADataModel($model);
        $this->givenAModelToReduceBy($modelToReduceBy);
        $this->whenReduceIsCalled();
        $this->thenTheNewModelIsReduced($expectedOutcome);
    }

    private function givenADataModel(MigrationDataModel $model)
    {
        $this->dataModel = $model;
    }

    private function givenAModelToReduceBy(MigrationDataModel $model)
    {
        $this->dataModelToReduceBy = $model;
    }

    private function whenReduceIsCalled()
    {
        $reducer = new MigrationDataModelReducer();
        $this->reducedModel = $reducer->reduceModelByModel($this->dataModel, $this->dataModelToReduceBy);
    }

    private function thenTheNewModelIsReduced(MigrationDataModel $model)
    {
        $this->assertEquals($this->reducedModel, $model);
    }

    public function getModelLists()
    {
        return [
            [
                [
                    'ChameleonSystemFooBundle' => new MigrationDataModel('ChameleonSystemFooBundle', ['1' => '', '2' => '']),
                    'ChameleonSystemBarBundle' => new MigrationDataModel('ChameleonSystemBarBundle', ['1' => '', '2' => '']),
                    'ChameleonSystemBazBundle' => new MigrationDataModel('ChameleonSystemBazBundle', ['1' => '', '2' => '']),
                ],
                [
                    'ChameleonSystemFooBundle' => new MigrationDataModel('ChameleonSystemFooBundle', ['1' => '']),
                    'ChameleonSystemBazBundle' => new MigrationDataModel('ChameleonSystemBazBundle', ['2' => '']),
                ],
                [
                    'ChameleonSystemFooBundle' => new MigrationDataModel('ChameleonSystemFooBundle', ['2' => '']),
                    'ChameleonSystemBarBundle' => new MigrationDataModel('ChameleonSystemBarBundle', ['1' => '', '2' => '']),
                    'ChameleonSystemBazBundle' => new MigrationDataModel('ChameleonSystemBazBundle', ['1' => '']),
                ],
            ],
        ];
    }

    /**
     * @test
     *
     * @dataProvider getModelLists
     */
    public function itReducesAModelList(array $model, array $modelToReduceBy, array $expectedOutcome)
    {
        $reducer = new MigrationDataModelReducer();
        $actual = $reducer->reduceModelListByModelList($model, $modelToReduceBy);

        $this->assertEquals($expectedOutcome, $actual);
    }
}
