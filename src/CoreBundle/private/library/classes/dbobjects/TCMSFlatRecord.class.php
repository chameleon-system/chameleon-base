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
 * "only-one-record tables should derive from this class. is a singleton version
 * of the TCMSRecord class
 * NOTE: you MUST overwrite the GetInstance function so that the correct sObjectName can be passed.
 * /**/
class TCMSFlatRecord extends TCMSRecord
{
    /**
     * holds the generated instances of TCMSFlatRecord.
     *
     * @var array
     */
    private static $aInstance = [];

    /**
     * Get a specific instance of a one-record-only table.
     *
     * @param string $sTableName - the table name
     * @param string $sFilterField - name of the field from which to load
     * @param mixed $sFilterValue - the field value identifying the record
     * @param int $iLanguageId - the language to load (if we are dealing with a multi-language table)
     * @param string $sObjectName - the class name from which we want to create an instance
     *
     * @return TCMSFlatRecord
     */
    public static function GetInstance($sTableName, $sFilterField = 'id', $sFilterValue = 1, $iLanguageId = null, $sObjectName = 'TCMSFlatRecord')
    {
        // static $aInstance = array();
        $sKey = $sObjectName.$sFilterField.$sFilterValue;
        if (!array_key_exists($sKey, self::$aInstance) || is_null(self::$aInstance[$sKey])) {
            $oInstance = new $sObjectName();
            $oInstance->table = $sTableName;
            if (!is_null($iLanguageId)) {
                $oInstance->SetLanguage($iLanguageId);
            }
            if ('id' == $sFilterField) {
                $oInstance->Load($sFilterValue);
            } else {
                $oInstance->LoadFromField($sFilterField, $sFilterValue);
            }
            self::$aInstance[$sKey] = $oInstance;
        }

        return self::$aInstance[$sKey];
    }
}
