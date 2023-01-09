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
    public function __construct($id = null, $iLanguageId = null)
    {
        parent::__construct('cms_tbl_conf_restrictions', $id, $iLanguageId);
    }

    /**
     * @deprecated Named constructors are deprecated and will be removed with PHP8. When calling from a parent, please use `parent::__construct` instead.
     * @see self::__construct
     */
    public function TCMSTableConfRestriction()
    {
        $this->callConstructorAndLogDeprecation(func_get_args());
    }

    public function GetRestriction($oTableConf)
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
