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
 * manages all user input.
/**/
class TCMSUserInput
{
    const FILTER_NONE = '';
    const FILTER_DEFAULT = 'TCMSUserInput_BaseText';
    const FILTER_SAFE_TEXT = 'TCMSUserInput_SafeText';
    const FILTER_SAFE_TEXTBLOCK = 'TCMSUserInput_SafeTextBlock';
    const FILTER_INT = 'TCMSUserInput_Int';
    const FILTER_DECIMAL = 'TCMSUserInput_Decimal';
    const FILTER_FILENAME = 'TCMSUserInput_Filename';
    const FILTER_DATE = 'TCMSUserInput_Date';
    const FILTER_URL = 'TCMSUserInput_URL';
    const FILTER_PASSWORD = 'TCMSUserInput_Password';
    const FILTER_URL_INTERNAL = 'TCMSUserInput_InternalURL';

    /**
     * return the filtered value.
     *
     * @param $sValue
     * @param $sFilterClass - form: classname;path;type|classname;path;type
     *
     * @return string
     *
     * @deprecated use chameleon_system_core.util.input_filter::filterValue() instead
     */
    public static function FilterValue($sValue, $sFilterClass)
    {
        static $aFilteredValueCache = array();
        $sCacheKey = '';
        if (is_array($sValue)) {
            $sCacheKey = md5(serialize($sValue));
        } else {
            $sCacheKey = md5($sValue);
        }

        $sCacheKey = $sFilterClass.'-'.$sCacheKey;
        if (array_key_exists($sCacheKey, $aFilteredValueCache)) {
            return $aFilteredValueCache[$sCacheKey];
        }

        $aFilters = self::GetFilterObject($sFilterClass);
        /** @var $oFilter TCMSUserInput_Raw */
        foreach ($aFilters as $oFilter) {
            $sValue = $oFilter->Filter($sValue);
        }
        $aFilteredValueCache[$sCacheKey] = $sValue;

        return $sValue;
    }

    /**
     * return a array that holds x filter objects of TCMSUserInputFilter or the subclasses.
     *
     * @param string $sFilterClass - form: classname;path;type|classname;path;type
     *
     * @return array
     *
     * @deprecated use chameleon_system_core.util.input_filter::getFilterObject() instead
     */
    public static function GetFilterObject($sFilterClass)
    {
        $aFilters = array();
        $aFilterClasses = explode('|', $sFilterClass);
        foreach ($aFilterClasses as $sFilter) {
            $aParts = explode(';', $sFilter);
            $sClassName = $aParts[0];
            $aFilters[] = new $sClassName();
        }

        return $aFilters;
    }
}
