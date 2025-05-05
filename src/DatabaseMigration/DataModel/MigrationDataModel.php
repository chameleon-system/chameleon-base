<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\DatabaseMigration\DataModel;

/**
 * The MigrationDataModel represents a collection of update filepaths belonging to a single bundle.
 */
class MigrationDataModel
{
    /**
     * @var string
     */
    private $bundleName;
    /**
     * @var array
     */
    private $buildNumberToFileMap;
    /**
     * @var array
     */
    private $duplicates = [];

    /**
     * @param string $bundleName
     * @param array $buildNumberToFileMap
     */
    public function __construct($bundleName, $buildNumberToFileMap = [])
    {
        $this->bundleName = $bundleName;
        $this->buildNumberToFileMap = $buildNumberToFileMap;
    }

    /**
     * @return string
     */
    public function getBundleName()
    {
        return $this->bundleName;
    }

    /**
     * @return array
     */
    public function getBuildNumberToFileMap()
    {
        return $this->buildNumberToFileMap;
    }

    /**
     * @return array
     */
    public function getDuplicates()
    {
        return $this->duplicates;
    }

    /**
     * @param int $buildNumber
     * @param string $path
     *
     * @return void
     */
    public function addFile($buildNumber, $path)
    {
        if (array_key_exists($buildNumber, $this->duplicates)
            && $this->duplicates[$buildNumber] !== $path) {
            $this->addDuplicate($buildNumber, $path);
        } elseif (array_key_exists($buildNumber, $this->buildNumberToFileMap)
            && $this->buildNumberToFileMap[$buildNumber] !== $path) {
            $this->addDuplicate($buildNumber, $this->buildNumberToFileMap[$buildNumber]);
            $this->addDuplicate($buildNumber, $path);
        } else {
            $this->buildNumberToFileMap[$buildNumber] = $path;
        }
    }

    /**
     * @param int $buildNumber
     * @param string $path
     *
     * @return void
     */
    private function addDuplicate($buildNumber, $path)
    {
        if (false === array_key_exists($buildNumber, $this->duplicates)) {
            $this->duplicates[$buildNumber] = [];
        }
        $this->duplicates[$buildNumber][] = $path;
    }
}
