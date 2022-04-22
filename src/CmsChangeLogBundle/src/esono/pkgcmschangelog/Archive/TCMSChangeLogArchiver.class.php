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
 * Archives and deletes old changelog sets and changelog items.
 *
 * @deprecated - replaced by \ChameleonSystem\CmsChangeLogBundle\DataAccess\CmsChangeLogDataAccess::deleteOlderThan()
/**/
class TCMSChangeLogArchiver
{
    /**
     * Archives and deletes all changelog data that is older than a specific date. The default value for this is 6 months.
     * If th.
     *
     * @return void
     */
    public function archiveAndDelete()
    {
        $bArchiveSuccessful = $this->archive();
        if ($bArchiveSuccessful) {
            $this->delete();
        }
    }

    /**
     * Archives the changelog data. Will call the generic table export named 'ArchiveChangeLog'.
     * If anything prevents this task from correctly archiving, it will return false to indicate that the corresponding data may not be deleted.
     *
     * @return bool true if the data has been successfully archived, else false
     */
    protected function archive()
    {
        if (!class_exists('TdbPkgGenericTableExport')) {
            return false;
        }
        $oExport = TdbPkgGenericTableExport::GetInstanceFromSystemName('ArchiveChangelog');
        if (!$oExport) {
            return false;
        }
        if (!$oExport->WriteExportToFile()) {
            return false;
        }

        $sDateExtension = '-'.date('Y-m-d-H-i-s');

        $sFilePathOld = $oExport->getExportFilePath();

        // delete export file if no data was written (assuming that there is always 1 header line)
        $file = fopen($sFilePathOld, 'rb');
        $iLineCount = 0;
        for ($i = 0; $i < 2; ++$i) {
            if (false !== fgets($file)) {
                ++$iLineCount;
            }
        }
        fclose($file);
        if ($iLineCount < 2) {
            unlink($sFilePathOld);

            return false; // simply return false so that no delete operation is attempted
        }

        $iLastDotPosition = strrpos($sFilePathOld, '.');
        if (false !== $iLastDotPosition) {
            $sFilePathNew = substr($sFilePathOld, 0, $iLastDotPosition).$sDateExtension.substr($sFilePathOld, $iLastDotPosition);
        } else {
            $sFilePathNew = $sFilePathOld.$sDateExtension;
        }

        rename($sFilePathOld, $sFilePathNew);

        return true;
    }

    /**
     * Delete all changelog sets and changelog items that are older than a specific date.
     *
     * @return void
     */
    protected function delete()
    {
        $sIdList = $this->getChangesetIDsToHandle();
        if ('' !== $sIdList) {
            $sQuery = 'DELETE FROM `pkg_cms_changelog_item`
                            WHERE `pkg_cms_changelog_item`.`pkg_cms_changelog_set_id` IN ('.$sIdList.')';
            MySqlLegacySupport::getInstance()->query($sQuery);

            $sQuery = 'DELETE FROM `pkg_cms_changelog_set`
                            WHERE `pkg_cms_changelog_set`.`id` IN ('.$sIdList.')';
            MySqlLegacySupport::getInstance()->query($sQuery);
        }
    }

    /**
     * Returns the IDs of changelog sets to be deleted. The implementation of this class might be changed, so that the return
     * value of this method also determines which changelog sets are to be archived.
     *
     * @return string A list of escaped changelog set IDs
     */
    protected function getChangesetIDsToHandle()
    {
        $sQuery = 'SELECT id
                   FROM `pkg_cms_changelog_set`
                  WHERE `pkg_cms_changelog_set`.`modify_date` < DATE_SUB(CURDATE(), INTERVAL '.$this->getThresholdDate().')';
        $oResult = MySqlLegacySupport::getInstance()->query($sQuery);

        $sError = MySqlLegacySupport::getInstance()->error();
        if ('' === $sError) {
            $sIdList = '';
            $bFirst = true;
            while ($row = MySqlLegacySupport::getInstance()->fetch_array($oResult, MYSQL_NUM)) {
                if (!$bFirst) {
                    $sIdList .= ',';
                }
                $sIdList .= "'".$row[0]."'";
                $bFirst = false;
            }
        } else {
            // TODO raise error
        }

        return '';
    }

    /**
     * Returns a string which specifies which changelog sets are to be deleted. This value will be appended to a MySQL INTERVAL declaration.
     * See https://dev.mysql.com/doc/refman/5.1/en/date-and-time-functions.html for possible values.
     * Override this method to specify a custom value.
     *
     * @return string the maximum age of changelog sets after the delete operation
     */
    protected function getThresholdDate()
    {
        return '6 MONTH';
    }
}
