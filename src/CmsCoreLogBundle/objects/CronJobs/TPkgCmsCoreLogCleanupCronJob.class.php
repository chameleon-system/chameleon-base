<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (!defined('PKG_CMS_CORE_LOG_DEFAULT_MAX_AGE_IN_SECONDS')) {
    /**
     * @deprecated since 6.3.0 - use Psr\Log\LoggerInterface in conjunction with Monolog logging instead
     */
    define('PKG_CMS_CORE_LOG_DEFAULT_MAX_AGE_IN_SECONDS', 2592000);
}
if (!defined('PKG_CMS_CORE_LOG_DEFAULT_MAX_AGE_IN_SECONDS_LEVEL_BELOW_WARNING')) {
    /**
     * @deprecated since 6.3.0 - use Psr\Log\LoggerInterface in conjunction with Monolog logging instead
     */
    define('PKG_CMS_CORE_LOG_DEFAULT_MAX_AGE_IN_SECONDS_LEVEL_BELOW_WARNING', 604800); // default to 7 days for notice and below
}

/**
 * @deprecated since 6.3.0 - use Psr\Log\LoggerInterface in conjunction with Monolog logging instead
 */
class TPkgCmsCoreLogCleanupCronJob extends TdbCmsCronjobs
{
    protected function _ExecuteCron()
    {
        parent::_ExecuteCron();
        $aChannelList = array();
        $oChannelList = TdbPkgCmsCoreLogChannelList::GetList();
        while ($oChannel = $oChannelList->Next()) {
            $aChannelList[] = MySqlLegacySupport::getInstance()->real_escape_string($oChannel->fieldName);
            $this->cleanChannel($oChannel->fieldName, $oChannel->fieldMaxLogAgeInSeconds, PKG_CMS_CORE_LOG_DEFAULT_MAX_AGE_IN_SECONDS_LEVEL_BELOW_WARNING);
        }

        $this->cleanOtherChannels($aChannelList, PKG_CMS_CORE_LOG_DEFAULT_MAX_AGE_IN_SECONDS, PKG_CMS_CORE_LOG_DEFAULT_MAX_AGE_IN_SECONDS_LEVEL_BELOW_WARNING);
    }

    private function cleanChannel($channelName, $iMaxAgeInSeconds, $iMaxAgeInSecondsForLevelBelowNotice)
    {
        $iMaxAgeInSeconds = intval($iMaxAgeInSeconds);
        $iMaxTime = time() - $iMaxAgeInSeconds;

        $iMaxAgeInSecondsForLevelBelowNotice = intval($iMaxAgeInSecondsForLevelBelowNotice);
        $iMaxNoticeTime = time() - $iMaxAgeInSecondsForLevelBelowNotice;

        $query = "DELETE FROM `pkg_cms_core_log`
                        WHERE `channel` = '".MySqlLegacySupport::getInstance()->real_escape_string($channelName)."'
                          AND (
                                (`timestamp` <=  {$iMaxTime} AND `level` > 200)
                                OR
                                (`timestamp` <=  {$iMaxNoticeTime} AND `level` <= 200)
                              )
                  ";
        MySqlLegacySupport::getInstance()->query($query);
    }

    private function cleanOtherChannels($aExcludeList, $iMaxAgeInSeconds, $iMaxAgeInSecondsForLevelBelowNotice)
    {
        $iMaxAgeInSeconds = intval($iMaxAgeInSeconds);
        $iMaxTime = time() - $iMaxAgeInSeconds;

        $iMaxAgeInSecondsForLevelBelowNotice = intval($iMaxAgeInSecondsForLevelBelowNotice);
        $iMaxNoticeTime = time() - $iMaxAgeInSecondsForLevelBelowNotice;

        $sChannelRestriction = '';
        if (count($aExcludeList) > 0) {
            $sChannelRestriction = "AND  `channel` NOT IN ('".implode("','", $aExcludeList)."'";
        }
        $query = "DELETE FROM `pkg_cms_core_log`
                        WHERE (
                                (`timestamp` <=  {$iMaxTime} AND `level` > 200)
                                OR
                                (`timestamp` <=  {$iMaxNoticeTime} AND `level` <= 200)
                              )
                        {$sChannelRestriction}
                  ";
        MySqlLegacySupport::getInstance()->query($query);
    }
}
