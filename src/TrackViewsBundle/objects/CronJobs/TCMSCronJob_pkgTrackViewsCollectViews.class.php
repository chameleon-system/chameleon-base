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
 * Collect all views from history and add them to view count.
 *
/**/
class TCMSCronJob_pkgTrackViewsCollectViews extends TCMSCronJob
{
    private $targetTable;
    private $timeToLive;

    /**
     * @param string $targetTable
     * @param string $timeToLive
     */
    public function __construct($targetTable, $timeToLive)
    {
        parent::TCMSCronJob();
        $this->targetTable = $targetTable;
        $this->timeToLive = $timeToLive;
    }

    /**
     * this array defines the time to live for counted tracking objects per table
     * if nothing set the default CHAMELEON_PKG_TRACKING_OBJECT_VIEW_HISTORY_TTL_IN_SEC is used.
     *
     * you can overwrite it like following for custom usage
     * i.e:  array('cms_document'=>86400)
     *
     * @var array
     */
    protected $aTableTTL = array();

    /**
     * this array defines date groups for specific tables.
     *
     * you can overwrite it like following for custom usage
     *
     * $aTableDateGroups = array('cms_document' => array('day', 'week', 'month', 'year', 'all')
     *
     * 'day', 'week', 'month', 'year' and 'all' are valid count periods
     *
     * 'day' => tracks items for every day (date('YmWd')).
     * 'week' => analog (date('YmWxx'))
     * 'month' => analog (date('Ymxxxx'))
     * 'year' => analog (date('Yxxxxxx'))
     * 'all' => total count 'xxxxxxxxxx'
     */
    protected $aTableDateGroups = array();

    protected function _ExecuteCron()
    {
        $targetTable = $this->targetTable;
        $dTime = time() - 5; // we use a 5 second delay to prevent double counting entries added in the same second as our cron job runs.
        $sDate = date('Y-m-d H:i:s', $dTime);
        $query = 'SELECT `id`, `table_name`, `owner_id`, COUNT(`owner_id`) AS views
                  FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($targetTable)."_history`
                 WHERE `item_counted` = '0'
                   AND `datecreated` <= '".MySqlLegacySupport::getInstance()->real_escape_string($sDate)."'
              GROUP BY `table_name`, `owner_id`";
        $tRes = MySqlLegacySupport::getInstance()->query($query);

        while ($aHistoryRow = MySqlLegacySupport::getInstance()->fetch_assoc($tRes)) {
            $aDateGroup = $this->getDateGroupsForTableName($aHistoryRow['table_name']);
            foreach ($aDateGroup as $sDateGroup) {
                $sViewCountId = $this->UpdateInsertViews($aHistoryRow, $sDateGroup);
            }
            $query = 'UPDATE `'.MySqlLegacySupport::getInstance()->real_escape_string($targetTable)."_history`
                     SET `item_counted` = '1', `".MySqlLegacySupport::getInstance()->real_escape_string($targetTable)."_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sViewCountId)."'
                   WHERE `owner_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($aHistoryRow['owner_id'])."'
                     AND `table_name` = '".MySqlLegacySupport::getInstance()->real_escape_string($aHistoryRow['table_name'])."'
                     AND `datecreated` <= '".MySqlLegacySupport::getInstance()->real_escape_string($sDate)."'";
            MySqlLegacySupport::getInstance()->query($query);
        }
        $this->clearTrackingObjectHistory($dTime);
    }

    /**
     * update or insert a new entry to view count and return the id of the inserted record.
     *
     * @param array  $aHistoryRow
     * @param string $sDate
     *
     * @return string
     */
    protected function UpdateInsertViews($aHistoryRow, $sDate)
    {
        $targetTable = $this->targetTable;
        // Custom Timestamp Format YYYYMMWWDD
        $aDateGroup = array('day' => date('YmWd'), 'week' => date('YmWxx'), 'month' => date('Ymxxxx'), 'year' => date('Yxxxxxx'), 'all' => 'xxxxxxxxxx');
        $query = 'SELECT *
                    FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($targetTable)."`
                   WHERE `table_name` = '".MySqlLegacySupport::getInstance()->real_escape_string($aHistoryRow['table_name'])."'
                     AND `owner_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($aHistoryRow['owner_id'])."'
                     AND `time_block` = '".$aDateGroup[$sDate]."'";
        $res = MySqlLegacySupport::getInstance()->query($query);
        if (!MySqlLegacySupport::getInstance()->num_rows($res) || 0 == MySqlLegacySupport::getInstance()->num_rows($res)) {
            $aData = array('table_name' => $aHistoryRow['table_name'], 'owner_id' => $aHistoryRow['owner_id'], 'count' => $aHistoryRow['views'], 'time_block' => $aDateGroup[$sDate]);

            /** @var TCMSRecord $sClassName */
            $sClassName = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $targetTable);

            $oViewCount = $sClassName::GetNewInstance();
            /** @var $oViewCount TCMSRecordWritable* */
            $oViewCount->LoadFromRow($aData);
            $oViewCount->AllowEditByAll(true);
            $oViewCount->Save();
            $oViewCount->AllowEditByAll(false);
            $sViewCountId = $oViewCount->id;
        } else {
            $aViewCountRow = MySqlLegacySupport::getInstance()->fetch_assoc($res);
            $sViewCountId = $aViewCountRow['id'];
            $sCount = intval($aHistoryRow['views']);
            $query = 'UPDATE `'.MySqlLegacySupport::getInstance()->real_escape_string($targetTable)."`
                       SET `table_name` = '".$aHistoryRow['table_name']."',
                           `owner_id` = '".$aHistoryRow['owner_id']."',
                           `count` = `count` + {$sCount}
                     WHERE `id` = '".$sViewCountId."'";
            MySqlLegacySupport::getInstance()->query($query);
        }

        return $sViewCountId;
    }

    /**
     *  get custom date group for table
     *  default is all options (day, week, month, year, all).
     *
     * @param $sTableName
     *
     * @return array
     */
    protected function getDateGroupsForTableName($sTableName)
    {
        // Custom Timestamp Format YYYYMMWWDD
        $aDateGroup = array('day', 'week', 'month', 'year', 'all');
        if (array_key_exists($sTableName, $this->aTableDateGroups)) {
            $aDateGroup = $this->aTableDateGroups[$sTableName];
        }

        return $aDateGroup;
    }

    protected function clearTrackingObjectHistory($dTime)
    {
        $targetTable = $this->targetTable;
        $sDate = date('Y-m-d H:i:s', $dTime);
        $query = 'SELECT  DISTINCT `table_name`
                  FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($targetTable)."_history`
                 WHERE `item_counted` = '1'
                   AND `datecreated` <= '".MySqlLegacySupport::getInstance()->real_escape_string($sDate)."'
                  ";
        $res = MySqlLegacySupport::getInstance()->query($query);
        $rows = MySqlLegacySupport::getInstance()->num_rows($res);
        if (!$rows || $rows > 0) {
            while ($aHistoryRow = MySqlLegacySupport::getInstance()->fetch_assoc($res)) {
                $this->deleteCountedItem($aHistoryRow['table_name'], $dTime);
            }
        }
    }

    /**
     * delete counted items that are expired.
     *
     * @param string $sTableName
     * @param float  $dTime
     */
    protected function deleteCountedItem($sTableName, $dTime)
    {
        $targetTable = $this->targetTable;
        $iExpired = $dTime - $this->getHistoryTimeToLive($sTableName);
        $sDate = date('Y-m-d H:i:s', $iExpired);
        $query = 'DELETE
                  FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($targetTable)."_history`
                 WHERE `item_counted` = '1'
                   AND `table_name` = '".MySqlLegacySupport::getInstance()->real_escape_string($sTableName)."'
                   AND `datecreated` <= '".MySqlLegacySupport::getInstance()->real_escape_string($sDate)."'
                  ";
        MySqlLegacySupport::getInstance()->query($query);
    }

    /**
     * get counted tracking item live time in sec.
     *
     * @param $sTableName
     *
     * @return string
     */
    protected function getHistoryTimeToLive($sTableName)
    {
        if (array_key_exists($sTableName, $this->aTableTTL)) {
            return $this->aTableTTL[$sTableName];
        }

        return $this->timeToLive;
    }
}
