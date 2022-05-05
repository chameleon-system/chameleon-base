<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * simple implementation of the bulk sql import
 * does a simple mysql insert for each AddRow call
 * this may be used by environments that are not allowed to use the LoadDataInfile method by mysql due to server restrictions.
 */
class TPkgCmsBulkSql_SimpleSql implements IPkgCmsBulkSql
{
    /**
     * the target table name.
     *
     * @var null|string
     */
    private $sTableName = null;

    /**
     * list of fields in target table.
     *
     * @var array
     */
    private $aFields = array();

    /**
     * returns true if the init was ok - else false.
     *
     * @param $sTable string the target table name
     * @param $aFields array list of fields in target table
     *
     * @return bool
     */
    public function Initialize($sTable, $aFields)
    {
        $this->sTableName = $sTable;
        $this->aFields = $aFields;

        return true;
    }

    /**
     * add a single row in the target table for given data.
     *
     * @param $aData
     *
     * @return bool
     */
    public function AddRow($aData)
    {
        $aEscapedFields = TTools::MysqlRealEscapeArray($this->aFields);
        $aValues = TTools::MysqlRealEscapeArray($aData);
        $query = "INSERT INTO `{$this->sTableName}` (`".implode('`,`', $aEscapedFields)."`) VALUES ('".implode("','", $aValues)."')";
        MySqlLegacySupport::getInstance()->query($query);

        $sError = MySqlLegacySupport::getInstance()->error();
        if (empty($sError)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * we don't need to do anything here. all is done by AddRow method in this implementation.
     *
     * @return bool
     */
    public function CommitData()
    {
        return true;
    }
}
