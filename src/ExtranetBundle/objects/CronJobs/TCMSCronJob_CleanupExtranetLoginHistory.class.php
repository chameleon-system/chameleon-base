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
 * removes all entries in login history below CHAMELEON_EXTRANET_LOGIN_HISTORY_LIMIT
 * false = no limit.
 * /**/
class TCMSCronJob_CleanupExtranetLoginHistory extends TdbCmsCronjobs
{
    /**
     * fetch a list of all user ids that have too many entries, then delete entries via subquery.
     *
     * @return void
     */
    protected function _ExecuteCron()
    {
        if (CHAMELEON_EXTRANET_LOGIN_HISTORY_LIMIT !== false && CHAMELEON_EXTRANET_LOGIN_HISTORY_LIMIT > 0) {
            $sQuery = 'SELECT COUNT(*) AS count, `data_extranet_user_login_history`.`data_extranet_user_id`
                        FROM `data_extranet_user_login_history`
                        GROUP BY `data_extranet_user_id`
                        HAVING COUNT(*) > '.MySqlLegacySupport::getInstance()->real_escape_string((string) CHAMELEON_EXTRANET_LOGIN_HISTORY_LIMIT);
            $rRes = MySqlLegacySupport::getInstance()->query($sQuery);
            while ($aRow = MySqlLegacySupport::getInstance()->fetch_assoc($rRes)) {
                $sSubQuery = "SELECT datecreated FROM (SELECT `datecreated` FROM `data_extranet_user_login_history`  WHERE `data_extranet_user_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($aRow['data_extranet_user_id'])."' ORDER BY `datecreated` DESC LIMIT ".MySqlLegacySupport::getInstance()->real_escape_string((string) CHAMELEON_EXTRANET_LOGIN_HISTORY_LIMIT).',1)';
                $sDeleteQuery = "DELETE FROM `data_extranet_user_login_history` WHERE `data_extranet_user_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($aRow['data_extranet_user_id'])."' AND `datecreated` <= (".$sSubQuery.' x)';
                MySqlLegacySupport::getInstance()->query($sDeleteQuery);
            }
        }
    }
}
