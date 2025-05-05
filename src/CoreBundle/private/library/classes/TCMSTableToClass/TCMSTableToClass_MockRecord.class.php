<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Doctrine\DBAL\Connection;

class TCMSTableToClass_MockRecord
{
    public $sqlData;
    public $table = '';
    public $id;
    private Connection $connection;

    public function __construct(Connection $connection, $sTable)
    {
        $this->table = $sTable;
        $this->connection = $connection;
    }

    public function LoadFromRow($aRow)
    {
        $this->sqlData = $aRow;
        $this->postLoadHook();
    }

    public function Load($sId)
    {
        $bFound = false;
        $query = "select * from {$this->table} where id = :id";
        if ($this->sqlData = $this->connection->fetchAssociative($query, ['id' => $sId])) {
            $this->postLoadHook();
            $bFound = true;
        }

        return $bFound;
    }

    private function postLoadHook()
    {
        $this->id = $this->sqlData['id'];
    }
}
