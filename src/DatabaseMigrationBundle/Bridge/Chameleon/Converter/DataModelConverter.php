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

class DataModelConverter
{
    /**
     * @var string
     */
    private $basePath;

    /**
     * @param string $basePath - the path which will be assumed as root, all update file paths will be relative to that
     */
    public function __construct($basePath)
    {
        $this->basePath = $basePath;
    }

    /**
     * @return \stdClass[]
     */
    private function convertModelToStdObjects(MigrationDataModel $dataModel)
    {
        $classes = [];
        $map = $dataModel->getBuildNumberToFileMap();
        ksort($map);
        foreach ($map as $buildNumber => $path) {
            $class = new \stdClass();
            $class->fileName = $path;
            $class->buildNumber = $buildNumber;
            $class->bundleName = $dataModel->getBundleName();
            $classes[] = $class;
        }

        return $classes;
    }

    /**
     * @param MigrationDataModel[] $models
     *
     * @return array[]
     */
    public function convertDataModelsToLegacySystem(array $models)
    {
        $oldModel = [];
        foreach ($models as $bundleName => $model) {
            $oldModel[$bundleName] = $this->convertModelToStdObjects($model);
        }

        return $oldModel;
    }
}
