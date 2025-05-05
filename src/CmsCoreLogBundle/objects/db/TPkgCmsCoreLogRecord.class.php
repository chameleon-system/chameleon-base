<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgCmsCoreLogRecord extends TPkgCmsCoreLogRecordAutoParent
{
    /**
     * @param int $iLevel
     * @param string $sField
     *
     * @return string|int
     */
    public static function cmsListCallbackLevel($iLevel, $aRow, $sField = '')
    {
        $aMapping = [
            100 => '<span style="background-color:#5bc0de;color:#fff; display: block;  padding: 3px;">'.$iLevel.': DEBUG</span>',
            200 => '<span style="background-color:#62c462;color:#fff; display: block;  padding: 3px;">'.$iLevel.': INFO</span>',
            250 => '<span style="background-color:#5bc0de;color:#fff;  display: block;  padding: 3px;">'.$iLevel.': NOTICE</span>',
            300 => '<span style="background-color:#f89406;color:#fff; font-weight: bold; display: block;  padding: 3px;">'.$iLevel.': WARNING</span>',
            400 => '<span style="background-color:#ee5f5b;color:#ffffff;font-weight: bold; display: block;  padding: 3px;">'.$iLevel.': ERROR</span>',
            500 => '<span style="background-color:#bd362f;color:#ffffff;font-weight: bold; display: block;  padding: 3px;">'.$iLevel.': CRITICAL</span>',
            550 => '<span style="background-color:#bd362f;color:#ffffff;font-weight: bold; display: block;  padding: 3px;">'.$iLevel.': ALERT</span>',
            600 => '<span style="background-color:red;color:#ffffff;font-weight: bold; display: block;  padding: 3px;">'.$iLevel.': EMERGENCY</span>',
        ];
        if (isset($aMapping[$iLevel])) {
            return $aMapping[$iLevel];
        } else {
            return $iLevel;
        }
    }
}
