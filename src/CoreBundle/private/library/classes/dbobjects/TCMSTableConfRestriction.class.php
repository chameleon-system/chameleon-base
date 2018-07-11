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
 * holds one restriction for a given table (defined in table conf. will be applied by the
 * listmanager to restrict the records shown).
/**/
class TCMSTableConfRestriction extends TCMSRecord
{
    public function TCMSTableConfRestriction($id = null, $iLanguageId = null)
    {
        parent::TCMSRecord('cms_tbl_conf_restrictions', $id, $iLanguageId);
    }

    public function GetRestriction(&$oTableConf)
    {
        $sRestriction = '';
        $sFunctionName = $this->sqlData['function'];
        if (function_exists($sFunctionName)) {
            $sRestriction = $sFunctionName($oTableConf, $this);
        } else {
            trigger_error('Unable to find function '.$this->sqlData['function'].' in TCMSTableConfRestriction', E_USER_WARNING);
        }

        return $sRestriction;
    }
}
