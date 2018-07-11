<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSTableToClass_MockRecord
{
    public $sqlData = null;
    public $table = '';
    public $id = null;

    public function __construct($sTable)
    {
        $this->table = $sTable;
    }

    public function LoadFromRow($aRow)
    {
        $this->sqlData = $aRow;
        $this->postLoadHook();
    }

    public function Load($sId)
    {
        $bFound = false;
        $query = "select * from {$this->table} where id = '".MySqlLegacySupport::getInstance()->real_escape_string($sId)."'";
        if ($this->sqlData = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query))) {
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
