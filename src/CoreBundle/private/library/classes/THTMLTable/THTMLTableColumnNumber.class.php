<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class THTMLTableColumnNumber extends THTMLTableColumn
{
    public const SELF_FIELD_DEF = 'THTMLTableColumnNumber,THTMLTable,Core';
    public const FILTER_FROM = 'from';
    public const FILTER_TO = 'to';

    /**
     * number of decimals to show. if set to null it will format each number with the
     * decimals found in that number.
     *
     * @var int
     */
    public $iNumberOfDecimals;

    /**
     * return sql restriction for the acting filter.
     *
     * @param array|string $sSearchFilter - the filter to use. SET THIS ONLY IF YOU WANT TO OVERWRITE THE CURRENT searchFilter!
     *                                    NOTE: if you pass a string, it will be used as the start and end value, if you pass an array,
     *                                    then use self::FILTER_FROM and self::FILTER_TO as index to set the from and/or to value
     *
     * @return string
     */
    public function GetFilterQueryString($sSearchFilter = null)
    {
        $sFilter = '';
        if (is_null($sSearchFilter)) {
            $sSearchFilter = $this->searchFilter;
        } elseif (!is_array($sSearchFilter)) {
            $sSearchFilter = [self::FILTER_FROM => $sSearchFilter, self::FILTER_TO => $sSearchFilter];
        }
        if (is_array($sSearchFilter)) {
            $sStartVal = '';
            $sEndVal = '';
            if (array_key_exists(self::FILTER_FROM, $sSearchFilter)) {
                $sStartVal = $sSearchFilter[self::FILTER_FROM];
            }
            if (array_key_exists(self::FILTER_TO, $sSearchFilter)) {
                $sEndVal = $sSearchFilter[self::FILTER_TO];
            }
            $oLocal = TCMSLocal::GetActive();
            if (!empty($sStartVal)) {
                $sStartVal = $oLocal->StringToNumber($sStartVal);
            }
            if (!empty($sEndVal)) {
                $sEndVal = $oLocal->StringToNumber($sEndVal);
            }

            if (!empty($sStartVal) && !empty($sEndVal)) {
                $sFilter = "{$this->sColumnDBName} >= ".MySqlLegacySupport::getInstance()->real_escape_string($sStartVal)." AND {$this->sColumnDBName} <= ".MySqlLegacySupport::getInstance()->real_escape_string($sEndVal);
            } elseif (!empty($sStartVal)) {
                $sFilter = "{$this->sColumnDBName} >= ".MySqlLegacySupport::getInstance()->real_escape_string($sStartVal);
            } elseif (!empty($sEndVal)) {
                $sFilter = "{$this->sColumnDBName} <= ".MySqlLegacySupport::getInstance()->real_escape_string($sEndVal);
            }
        }

        return $sFilter;
    }

    /**
     * method used to format the given value. overwrite this method for every column type you write.
     *
     * @param string $sValue
     * @param TCMSRecord $oTableRow
     *
     * @return string
     */
    protected function FormatValue($sValue, $oTableRow)
    {
        $oLocal = TCMSLocal::GetActive();
        $iNumberOfDecimals = $this->iNumberOfDecimals;
        if (is_null($iNumberOfDecimals)) {
            $iNumberOfDecimals = 0;
            if (false !== strpos($sValue, '.')) {
                $dDecimal = $sValue - floor($sValue);
                $sDecimal = substr($dDecimal, 2);
                $iNumberOfDecimals = strlen($sDecimal);
            }
        }

        return TGlobal::OutHTML($oLocal->FormatNumber($sValue, $iNumberOfDecimals));
    }

    /**
     * returns a css class used for the column.
     *
     * @return string
     */
    public function GetColumnFormatCSSClass()
    {
        return 'THTMLTableColumnNumber';
    }

    protected function GetViewPath()
    {
        return 'THTMLTable/THTMLTableColumnNumber';
    }
}
