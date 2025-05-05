<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

interface IPkgCmsBulkSql
{
    /**
     * @param string $sTable
     * @param string[] $aFields
     *
     * @return bool
     */
    public function Initialize($sTable, $aFields);

    /**
     * @param array<string, string> $aData
     *
     * @return bool
     */
    public function AddRow($aData);

    /**
     * @return bool
     */
    public function CommitData();
}
