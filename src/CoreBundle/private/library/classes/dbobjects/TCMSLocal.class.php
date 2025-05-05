<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;

class TCMSLocal extends TCMSRecord
{
    protected $dNumberOfDecimals = 2;
    public $sDecimalSeperator = ',';
    public $sThousandSeperator = '.';
    public $sActiveLocal;

    //  128,64,32,16 8,4,2,1
    public const DATEFORMAT_SHOW_ALL = 238; // 1110 1110
    public const DATEFORMAT_SHOW_DATE = 224; // 1110 0000
    public const DATEFORMAT_SHOW_TIME = 14; // 0000 1110

    public const DATEFORMAT_SHOW_DATE_YEAR = 128; // 1000 0000
    public const DATEFORMAT_SHOW_DATE_MONTH = 64; // 0100 0000
    public const DATEFORMAT_SHOW_DATE_DAY = 32; // 0010 0000

    public const DATEFORMAT_SHOW_TIME_HOUR = 8; // 0000 1000
    public const DATEFORMAT_SHOW_TIME_MINUTE = 4; // 0000 0100
    public const DATEFORMAT_SHOW_TIME_SECOND = 2; // 0000 0010

    public function __construct($id = null, $iLanguage = null)
    {
        parent::__construct('cms_locals', $id, $iLanguage);
    }

    protected function PostLoadHook()
    {
        parent::PostLoadHook();
        $aNumPars = explode('|', $this->sqlData['numbers']);
        if (3 == count($aNumPars)) {
            if (is_int($aNumPars[0])) {
                $this->dNumberOfDecimals = $aNumPars[0];
            }
            $this->sDecimalSeperator = $aNumPars[1];
            $this->sThousandSeperator = $aNumPars[2];
        }
    }

    /**
     * return active local.
     *
     * @return TdbCmsLocals|false Only returns false if there is an initialization loop. It is not necessary to consider
     *                            this normally, but only in code that is potentially used in request bootstrapping context (including autoclasses).
     */
    public static function GetActive()
    {
        static $oActive = false;
        static $bLastInitDidNotCountBecauseNoActivePageWasLoaded = false;
        static $bCurrentlyLoading = false;
        if (!$bCurrentlyLoading && (!$oActive || $bLastInitDidNotCountBecauseNoActivePageWasLoaded)) {
            // always use TdbCmsLocals to get instance
            if ('TCMSLocal' === get_called_class()) {
                return TdbCmsLocals::GetActive();
            }

            $bCurrentlyLoading = true;
            $id = 1;
            $oGlobal = TGlobal::instance();
            if (!$oGlobal->IsCMSMode()) {
                $activePortal = self::getPortalDomainService()->getActivePortal();
                if (!is_null($activePortal)) {
                    $id = $activePortal->sqlData['cms_locals_id'];
                    $bLastInitDidNotCountBecauseNoActivePageWasLoaded = false;
                } else {
                    $bLastInitDidNotCountBecauseNoActivePageWasLoaded = true;
                }
                if (!$oActive || $id != $oActive->id) {
                    $oActive = TdbCmsLocals::GetNewInstance();
                    $oActive->Load($id);
                    $oActive->InitPHPLocalSetting();
                }
            } else {
                /* @TODO: at some point we will want to fetch this from the user. for now we just take id=1 */
                if (!$oActive || $id != $oActive->id) {
                    if (class_exists('TdbCmsLocals')) {
                        $oActive = TdbCmsLocals::GetNewInstance();
                    } else {
                        $oActive = new self();
                    }
                    $oActive->Load($id);
                    $oActive->InitPHPLocalSetting();
                }
            }
            $bCurrentlyLoading = false;
        }

        return $oActive;
    }

    public function InitPHPLocalSetting()
    {
        if (!empty($this->sqlData['php_local_name'])) {
            $sLocalString = trim($this->sqlData['php_local_name']);
            $aLocals = explode(',', $sLocalString);
            // take at moste for
            $maxCount = 4;
            if (count($aLocals) < 4) {
                $maxCount = count($aLocals);
            }
            switch ($maxCount) {
                case 1:
                    $aLocals[0] = trim($aLocals[0]);
                    $this->sActiveLocal = setlocale(LC_ALL, $aLocals[0]);
                    break;
                case 2:
                    $aLocals[0] = trim($aLocals[0]);
                    $aLocals[1] = trim($aLocals[1]);
                    $this->sActiveLocal = setlocale(LC_ALL, $aLocals[0], $aLocals[1]);
                    break;
                case 3:
                    $aLocals[0] = trim($aLocals[0]);
                    $aLocals[1] = trim($aLocals[1]);
                    $aLocals[2] = trim($aLocals[2]);
                    $this->sActiveLocal = setlocale(LC_ALL, $aLocals[0], $aLocals[1], $aLocals[2]);
                    break;
                case 4:
                    $aLocals[0] = trim($aLocals[0]);
                    $aLocals[1] = trim($aLocals[1]);
                    $aLocals[2] = trim($aLocals[2]);
                    $aLocals[3] = trim($aLocals[3]);
                    $this->sActiveLocal = setlocale(LC_ALL, $aLocals[0], $aLocals[1], $aLocals[2], $aLocals[3]);

                    break;
                default:
                    break;
            }
        }
    }

    /**
     * return formated number.
     *
     * @param numeric $dNumber
     * @param int $dDecimals
     *
     * @return string
     */
    public function FormatNumber($dNumber, $dDecimals = null)
    {
        $dLocalDecimals = $this->dNumberOfDecimals;
        if (!is_null($dDecimals)) {
            $dLocalDecimals = $dDecimals;
        }

        return number_format((float) $dNumber, $dLocalDecimals, $this->sDecimalSeperator, $this->sThousandSeperator);
    }

    /**
     * Converts string to decimal.
     *
     * @param string $sNumber
     *
     * @return float
     */
    public function StringToNumber($sNumber)
    {
        $sNumber = str_replace($this->sThousandSeperator, '', $sNumber);
        $sNumber = str_replace($this->sDecimalSeperator, '.', $sNumber);
        $dNumber = floatval($sNumber);

        return $dNumber;
    }

    /**
     * format an sql style date into the current local
     * Note: by using iShowDateParte you can show parts of the date. just set the bitmask using self::DATEFORMAT_SHOW_*.
     *
     * @example: show the full date but only the hours and minutes of the time part:
     *   $oLocal->FormatDate('2008-02-03 09:10:13',TCMSLocal::DATEFORMAT_SHOW_DATE | TCMSLocal::DATEFORMAT_SHOW_TIME_HOUR | TCMSLocal::DATEFORMAT_SHOW_TIME_MINUTE)
     *
     * @param string $sqlDateString - date of the form yyyy-mm-dd HH:MM:SS
     * @param int $iShowDatePart - show which part of the date
     *
     * @return string
     */
    public function FormatDate($sqlDateString, $iShowDatePart = self::DATEFORMAT_SHOW_ALL)
    {
        // input:  YYYY-MM-DD HH:II:SS
        $sqlDateString = trim($sqlDateString);
        if ('0000-00-00' == $sqlDateString || '0000-00-00 00:00:00' == $sqlDateString) {
            $sqlDateString = '';
        }
        $sDate = $sqlDateString;
        $parts = explode(' ', $sqlDateString); // split into date and time
        if (is_array($parts) && count($parts) > 0 && !empty($parts[0])) {
            $list = explode('-', $parts[0]);

            $sDate = $this->sqlData['date_format'];
            // kick any "do-not-show" parts...
            if (self::DATEFORMAT_SHOW_DATE_DAY != (self::DATEFORMAT_SHOW_DATE_DAY & $iShowDatePart)) {
                $sDate = $this->KickFormatPart($sDate, 'd', ['m', 'y']);
            }
            if (self::DATEFORMAT_SHOW_DATE_MONTH != (self::DATEFORMAT_SHOW_DATE_MONTH & $iShowDatePart)) {
                $sDate = $this->KickFormatPart($sDate, 'm', ['d', 'y']);
            }
            if (self::DATEFORMAT_SHOW_DATE_YEAR != (self::DATEFORMAT_SHOW_DATE_YEAR & $iShowDatePart)) {
                $sDate = $this->KickFormatPart($sDate, 'y', ['m', 'd']);
            }
            $sDate = $this->StripStringParts($sDate, ['d', 'm', 'y'], true);
            $sDate = str_replace(['d', 'm', 'y'], [$list[2], $list[1], $list[0]], $sDate);

            $sTime = '';
            if (!empty($parts[1])) {
                $aTimeList = explode(':', $parts[1]);
                if (3 == count($aTimeList)) {
                    $sTime = $this->sqlData['time_format'];
                    if (self::DATEFORMAT_SHOW_TIME_SECOND != (self::DATEFORMAT_SHOW_TIME_SECOND & $iShowDatePart)) {
                        $sTime = $this->KickFormatPart($sTime, 's', ['m', 'h']);
                    }
                    if (self::DATEFORMAT_SHOW_TIME_MINUTE != (self::DATEFORMAT_SHOW_TIME_MINUTE & $iShowDatePart)) {
                        $sTime = $this->KickFormatPart($sTime, 'm', ['s', 'h']);
                    }
                    if (self::DATEFORMAT_SHOW_TIME_HOUR != (self::DATEFORMAT_SHOW_TIME_HOUR & $iShowDatePart)) {
                        $sTime = $this->KickFormatPart($sTime, 'h', ['s', 'm']);
                    }

                    // strip any non valid chars from the back
                    $sTime = $this->StripStringParts($sTime, ['h', 'm', 's'], true);
                    $sTime = str_replace(['h', 'm', 's'], [$aTimeList[0], $aTimeList[1], $aTimeList[2]], $sTime);
                }
            }
            if (!empty($sDate) && !empty($sTime)) {
                $sDate .= ' ';
            }
            $sDate .= $sTime;
        }

        return $sDate;
    }

    protected function KickFormatPart($sFormatString, $sKickPart, $aSafeParts)
    {
        $aNewParts = [];
        $aParts = explode($sKickPart, $sFormatString);
        $iCount = 0;
        foreach ($aParts as $iKey => $sPart) {
            if (!empty($sPart)) {
                $aNewParts[] = $this->StripStringParts($sPart, $aSafeParts);
                ++$iCount;
            }
        }

        return implode('', $aNewParts);
    }

    protected function StripStringParts($sString, $aSafeParts, $bReverse = false)
    {
        $newString = '';
        // ?????x??
        if ($bReverse) {
            $iPos = 0;
            foreach ($aSafeParts as $sPart) {
                $iTmpPos = strrpos($sString, $sPart);
                if (false !== $iTmpPos) {
                    $iTmpPos += strlen($sPart);
                    if ($iTmpPos > $iPos) {
                        $iPos = $iTmpPos;
                    }
                }
            }
            $newString = substr($sString, 0, $iPos);
        } else {
            $iPos = strlen($sString);
            foreach ($aSafeParts as $sPart) {
                $iTmpPos = strpos($sString, $sPart);
                if (false !== $iTmpPos && ($iTmpPos < $iPos)) {
                    $iPos = $iTmpPos;
                }
            }
            $newString = substr($sString, $iPos, strlen($sString) - $iPos);
        }

        return $newString;
    }

    /**
     * convert a date string in local format into an sql date.
     *
     * @param string $sDateString
     *
     * @return string
     */
    public function StringToDate($sDateString)
    {
        $parts = explode(' ', $sDateString); // split into date and time
        $sTimePart = '';
        $sDatePart = $parts[0];
        if (count($parts) > 1) {
            $sTimePart = $parts[1];
        }

        // get date part
        $aDateParts = [0 => '', 1 => '', 2 => ''];
        $sDateFormatString = str_replace(['d', 'm', 'y'], ['%d', '%d', '%d'], $this->sqlData['date_format']);
        sscanf($sDatePart, $sDateFormatString, $aDateParts[0], $aDateParts[1], $aDateParts[2]);
        $aPosData = [];
        $aPosData[strpos($this->sqlData['date_format'], 'd')] = 'day';
        $aPosData[strpos($this->sqlData['date_format'], 'm')] = 'month';
        $aPosData[strpos($this->sqlData['date_format'], 'y')] = 'year';
        ksort($aPosData);
        $iIndex = 0;
        $aNewDateParts = ['day' => '', 'month' => '', 'year' => ''];
        foreach ($aPosData as $pos => $type) {
            $aNewDateParts[$type] = $aDateParts[$iIndex];
            ++$iIndex;
        }
        $month = $aNewDateParts['month'];
        if ($month <= 9) {
            $month = '0'.$month;
        }

        $day = $aNewDateParts['day'];
        if ($day <= 9) {
            $day = '0'.$day;
        }

        $sSQLDate = $aNewDateParts['year'].'-'.$month.'-'.$day;

        // now do time part...
        if (!empty($sTimePart)) {
            $aTimeParts = [0 => '', 1 => '', 2 => ''];
            $sTimeFormatString = str_replace(['h', 'm', 's'], ['%d', '%d', '%d'], $this->sqlData['time_format']);
            sscanf($sTimePart, $sTimeFormatString, $aTimeParts[0], $aTimeParts[1], $aTimeParts[2]);

            // find positions...
            $aPosData = [];
            $aPosData[strpos($this->sqlData['time_format'], 'h')] = 'hour';
            $aPosData[strpos($this->sqlData['time_format'], 'm')] = 'minute';
            $aPosData[strpos($this->sqlData['time_format'], 's')] = 'second';
            ksort($aPosData);
            $iIndex = 0;
            $aNewTimeParts = ['hour' => '', 'minute' => '', 'second' => ''];
            foreach ($aPosData as $pos => $type) {
                $aNewTimeParts[$type] = $aTimeParts[$iIndex];
                ++$iIndex;
            }
            $sSQLDate .= ' '.$aNewTimeParts['hour'].':'.$aNewTimeParts['minute'].':'.$aNewTimeParts['second'];
        }

        return $sSQLDate;
    }

    /**
     * @return PortalDomainServiceInterface
     */
    private static function getPortalDomainService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.portal_domain_service');
    }
}
