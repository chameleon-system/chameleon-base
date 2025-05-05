<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\DatabaseMigration\Reducer;

use ChameleonSystem\DatabaseMigration\DataModel\MigrationDataModel;

class MigrationDataModelReducer
{
    /**
     * @return MigrationDataModel
     */
    public function reduceModelByModel(MigrationDataModel $modelToReduce, MigrationDataModel $modelToReduceBy)
    {
        $reducedModel = new MigrationDataModel($modelToReduce->getBundleName());
        $originalFileList = $modelToReduce->getBuildNumberToFileMap();
        $fileListToReduceBy = $modelToReduceBy->getBuildNumberToFileMap();
        $buildNumbersToRemove = array_keys($fileListToReduceBy);
        foreach ($originalFileList as $buildNumber => $filePath) {
            if (!in_array($buildNumber, $buildNumbersToRemove)) {
                $reducedModel->addFile($buildNumber, $filePath);
            }
        }

        return $reducedModel;
    }

    /**
     * @param MigrationDataModel[] $modelsToReduce
     * @param MigrationDataModel[] $modelsToReduceBy
     *
     * @return MigrationDataModel[]
     */
    public function reduceModelListByModelList(array $modelsToReduce, array $modelsToReduceBy)
    {
        $reducedList = [];

        foreach ($modelsToReduce as $bundleName => $modelToReduce) {
            if (false === isset($modelsToReduceBy[$bundleName])) {
                $reducedList[$bundleName] = $modelToReduce;
                continue;
            }

            $modelsToReduceByFileMap = $modelsToReduceBy[$bundleName]->getBuildNumberToFileMap();
            $reducedListByCounterName = [];
            foreach ($modelToReduce->getBuildNumberToFileMap() as $buildNumber => $path) {
                if (false === isset($modelsToReduceByFileMap[$buildNumber])) {
                    $reducedListByCounterName[$buildNumber] = $path;
                }
            }
            if (count($reducedListByCounterName) > 0) {
                $reducedList[$bundleName] = new MigrationDataModel($bundleName, $reducedListByCounterName);
            }
        }

        return $reducedList;
    }
}
