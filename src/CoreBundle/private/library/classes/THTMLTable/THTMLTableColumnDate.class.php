<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class THTMLTableColumnDate extends THTMLTableColumn
{
    public const SELF_FIELD_DEF = 'THTMLTableColumnDate,THTMLTable,Core';
    public const FILTER_FROM = 'from';
    public const FILTER_TO = 'to';

    /**
     * set this to controll wich parts of the date should be shown (use the constants from TCMSLocal::DATEFORMAT_*).
     *
     * @var int
     */
    public $iDateFormatType = TCMSLocal::DATEFORMAT_SHOW_ALL;

    /**
     * return sql restriction for the acting filter.
     *
     * @param array|string $sSearchFilter - the filter to use. SET THIS ONLY IF YOU WANT TO OVERWRITE THE CURRENT searchFilter!
     *                                    NOTE: if you pass a string, it will be used as the start and end value, if you pass an array,
     *                                    then use self::FILTER_FROM and self::FILTER_TO as index to set the from and/or to date
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
                $sStartVal = $oLocal->StringToDate($sStartVal);
            }
            if (!empty($sEndVal)) {
                $sEndVal = $oLocal->StringToDate($sEndVal);
            }

            if (!empty($sStartVal) && !empty($sEndVal)) {
                $sFilter = "{$this->sColumnDBName} >= '".MySqlLegacySupport::getInstance()->real_escape_string($sStartVal)."' AND {$this->sColumnDBName} <= '".MySqlLegacySupport::getInstance()->real_escape_string($sEndVal)."'";
            } elseif (!empty($sStartVal)) {
                $sFilter = "{$this->sColumnDBName} >= '".MySqlLegacySupport::getInstance()->real_escape_string($sStartVal)."'";
            } elseif (!empty($sEndVal)) {
                $sFilter = "{$this->sColumnDBName} <= '".MySqlLegacySupport::getInstance()->real_escape_string($sEndVal)."'";
            }
        }

        return $sFilter;
    }

    protected function GetViewPath()
    {
        return 'THTMLTable/THTMLTableColumnDate';
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

        return TGlobal::OutHTML($oLocal->FormatDate($sValue, $this->iDateFormatType));
    }

    /**
     * returns a css class used for the column.
     *
     * @return string
     */
    public function GetColumnFormatCSSClass()
    {
        return 'THTMLTableColumnDate';
    }
}
