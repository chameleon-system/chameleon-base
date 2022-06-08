<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSTableEditorNewsletterCampaign extends TCMSTableEditor
{
    /**
     * @var int
     */
    protected $iSubscribersAddedToQueue = 0;

    /**
     * gets called after save if all posted data was valid.
     *
     * @param TIterator  $oFields    holds an iterator of all field classes from DB table with the posted values or default if no post data is present
     * @param TCMSRecord $oPostTable holds the record object of all posted data
     *
     * @return void
     */
    protected function PostSaveHook(&$oFields, &$oPostTable)
    {
        parent::PostSaveHook($oFields, $oPostTable);
        $oNewsletterGroupList = &$oPostTable->GetMLT('pkg_newsletter_group_mlt');
        if ($oNewsletterGroupList->Length() > 0 && '1' == $oPostTable->sqlData['active']) {
            // to allow fast insert, we work with a tmp table
            $query = 'CREATE TEMPORARY TABLE `_tmp_pkg_newsletter_queue` (
                  `pkg_newsletter_user` CHAR(36) NOT NULL,
                  `pkg_newsletter_campaign_id` CHAR( 36 ) NOT NULL,
                  `email` CHAR( 255 ) NOT NULL
                  )';
            MySqlLegacySupport::getInstance()->query($query);

            // now add index
            $query = 'ALTER TABLE `_tmp_pkg_newsletter_queue` ADD INDEX ( `pkg_newsletter_user`) ';
            MySqlLegacySupport::getInstance()->query($query);
            $query = 'ALTER TABLE `_tmp_pkg_newsletter_queue` ADD INDEX ( `pkg_newsletter_campaign_id`) ';
            MySqlLegacySupport::getInstance()->query($query);
            $query = 'ALTER TABLE `_tmp_pkg_newsletter_queue` ADD INDEX ( `email`) ';
            MySqlLegacySupport::getInstance()->query($query);

            while ($oNewsletterGroup = $oNewsletterGroupList->Next()) {
                $iNewsletterGroup = $oNewsletterGroup->id;

                $oPkgNewsletterGroup = TdbPkgNewsletterGroup::GetNewInstance();
                /** @var $oPkgNewsletterGroup TdbPkgNewsletterGroup */
                $oPkgNewsletterGroup->Load($iNewsletterGroup);

                $this->AddUsersToTmpTable($oPkgNewsletterGroup);

                // remove robinson users
                $query = "DELETE `_tmp_pkg_newsletter_queue`.*
                     FROM `_tmp_pkg_newsletter_queue`
               INNER JOIN `pkg_newsletter_robinson` ON `_tmp_pkg_newsletter_queue`.`email` = `pkg_newsletter_robinson`.`email`
                    WHERE `pkg_newsletter_robinson`.`cms_portal_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($oPkgNewsletterGroup->fieldCmsPortalId)."'
                      AND `_tmp_pkg_newsletter_queue`.`pkg_newsletter_campaign_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->sId)."'
                  ";
                MySqlLegacySupport::getInstance()->query($query);
            }

            $query = "DELETE FROM `pkg_newsletter_queue`
                        WHERE `pkg_newsletter_campaign_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->sId)."'
                          AND `date_sent` = '0000-00-00 00:00:00'
                 ";
            MySqlLegacySupport::getInstance()->query($query);

            // drop members already in queue..
            $query = "DELETE `_tmp_pkg_newsletter_queue`.*
                   FROM `_tmp_pkg_newsletter_queue`
             INNER JOIN `pkg_newsletter_queue` ON _tmp_pkg_newsletter_queue`.`pkg_newsletter_user` = `pkg_newsletter_queue`.`pkg_newsletter_user`
                  WHERE `pkg_newsletter_queue`.`pkg_newsletter_campaign_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->sId)."'
                ";
            MySqlLegacySupport::getInstance()->query($query);

            //remove users without signup acception
            $query = "DELETE `_tmp_pkg_newsletter_queue`.*
                   FROM `_tmp_pkg_newsletter_queue`
             INNER JOIN `pkg_newsletter_user` ON `_tmp_pkg_newsletter_queue`.`pkg_newsletter_user` = `pkg_newsletter_user`.`id`
                  WHERE `pkg_newsletter_user`.`optin` = '0'
                ";
            MySqlLegacySupport::getInstance()->query($query);

            // now insert into spooler
            $query = "INSERT INTO `pkg_newsletter_queue` (`id`,`pkg_newsletter_campaign_id`,`pkg_newsletter_user`)
                       SELECT DISTINCT MD5(CONCAT(`_tmp_pkg_newsletter_queue`.`pkg_newsletter_campaign_id`, `_tmp_pkg_newsletter_queue`.`pkg_newsletter_user`)),
                              `_tmp_pkg_newsletter_queue`.`pkg_newsletter_campaign_id`, `_tmp_pkg_newsletter_queue`.`pkg_newsletter_user`
                         FROM `_tmp_pkg_newsletter_queue`
                        WHERE `_tmp_pkg_newsletter_queue`.`pkg_newsletter_campaign_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->sId)."'
                 ";
            MySqlLegacySupport::getInstance()->query($query);
            $this->iSubscribersAddedToQueue = MySqlLegacySupport::getInstance()->affected_rows();
            $this->setNewsletterQueueCount();
            TCacheManager::PerformeTableChange('pkg_newsletter_queue');
        }
    }

    /**
     * @return void
     */
    protected function setNewsletterQueueCount()
    {
        $sQuery = "SELECT COUNT(*) AS count FROM  `pkg_newsletter_queue` WHERE `pkg_newsletter_campaign_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->sId)."'";
        $res = MySqlLegacySupport::getInstance()->query($sQuery);
        $aRow = MySqlLegacySupport::getInstance()->fetch_assoc($res);
        $this->SaveField('send_statistics', $aRow['count']);
    }

    /**
     * add users to the the _tmp_pkg_newsletter_queue table.
     *
     * @param TdbPkgNewsletterGroup $oPkgNewsletterGroup - the user group we are working on
     *
     * @return void
     */
    protected function AddUsersToTmpTable(&$oPkgNewsletterGroup)
    {
        // if we have include_all_newsletter_users set, we can skip all other settings and just add everyone
        if ($oPkgNewsletterGroup->fieldIncludeAllNewsletterUsers) {
            $query = "INSERT INTO `_tmp_pkg_newsletter_queue` (`pkg_newsletter_user`,`email`,`pkg_newsletter_campaign_id`)
                       SELECT `pkg_newsletter_user`.`id`,`pkg_newsletter_user`.`email`,  '".MySqlLegacySupport::getInstance()->real_escape_string($this->sId)."' AS pkg_newsletter_campaign_id
                         FROM `pkg_newsletter_user`
                        WHERE `pkg_newsletter_user`.`cms_portal_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($oPkgNewsletterGroup->fieldCmsPortalId)."'
                 ";
            MySqlLegacySupport::getInstance()->query($query);
        } else {
            // if the include_newsletter_user_not_assigned_to_any_group bit is set, we add all group free users
            if ($oPkgNewsletterGroup->fieldIncludeNewsletterUserNotAssignedToAnyGroup) {
                $query = "INSERT INTO `_tmp_pkg_newsletter_queue` (`pkg_newsletter_user`,`email`,`pkg_newsletter_campaign_id`)
                         SELECT `pkg_newsletter_user`.`id`,`pkg_newsletter_user`.`email`,  '".MySqlLegacySupport::getInstance()->real_escape_string($this->sId)."' AS pkg_newsletter_campaign_id
                           FROM `pkg_newsletter_user`
                      LEFT JOIN `pkg_newsletter_user_pkg_newsletter_group_mlt` ON `pkg_newsletter_user`.`id` = `pkg_newsletter_user_pkg_newsletter_group_mlt`.`source_id`
                          WHERE `pkg_newsletter_user`.`cms_portal_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($oPkgNewsletterGroup->fieldCmsPortalId)."'
                       GROUP BY `pkg_newsletter_user`.`id`
                         HAVING COUNT(`pkg_newsletter_user_pkg_newsletter_group_mlt`.`source_id`) = 0
                   ";
                MySqlLegacySupport::getInstance()->query($query);
            } else {
                // first, insert the users that have actively joined the newsletter
                $query = "INSERT INTO `_tmp_pkg_newsletter_queue` (`pkg_newsletter_user`,`email`,`pkg_newsletter_campaign_id`)
                         SELECT `pkg_newsletter_user`.`id`,`pkg_newsletter_user`.`email`,  '".MySqlLegacySupport::getInstance()->real_escape_string($this->sId)."' AS pkg_newsletter_campaign_id
                           FROM `pkg_newsletter_user`
                     INNER JOIN `pkg_newsletter_user_pkg_newsletter_group_mlt` ON `pkg_newsletter_user`.`id` = `pkg_newsletter_user_pkg_newsletter_group_mlt`.`source_id`
                          WHERE `pkg_newsletter_user_pkg_newsletter_group_mlt`.`target_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($oPkgNewsletterGroup->id)."'
                            AND `pkg_newsletter_user`.`cms_portal_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($oPkgNewsletterGroup->fieldCmsPortalId)."'
                   ";
                MySqlLegacySupport::getInstance()->query($query);
            }

            // if the include_all_newsletter_users_with_no_extranet_account is set, we include all users that do not have an extranet account
            if ($oPkgNewsletterGroup->fieldIncludeAllNewsletterUsersWithNoExtranetAccount) {
                $query = "INSERT INTO `_tmp_pkg_newsletter_queue` (`pkg_newsletter_user`,`email`,`pkg_newsletter_campaign_id`)
                         SELECT `pkg_newsletter_user`.`id`,`pkg_newsletter_user`.`email`,  '".MySqlLegacySupport::getInstance()->real_escape_string($this->sId)."' AS pkg_newsletter_campaign_id
                           FROM `pkg_newsletter_user`
                          WHERE (`pkg_newsletter_user`.`data_extranet_user_id` = '' OR `pkg_newsletter_user`.`data_extranet_user_id` = '0')
                            AND `pkg_newsletter_user`.`cms_portal_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($oPkgNewsletterGroup->fieldCmsPortalId)."'
                   ";
                MySqlLegacySupport::getInstance()->query($query);
            }

            // if we have any users connected via data_extranet_group_mlt we include them
            $aExtranetGroups = $oPkgNewsletterGroup->GetMLTIdList('data_extranet_group_mlt');
            if (count($aExtranetGroups) > 0) {
                $aExtranetGroups = TTools::MysqlRealEscapeArray($aExtranetGroups);
                $query = "INSERT INTO `_tmp_pkg_newsletter_queue` (`pkg_newsletter_user`,`email`,`pkg_newsletter_campaign_id`)
                         SELECT `pkg_newsletter_user`.`id`,`pkg_newsletter_user`.`email`,  '".MySqlLegacySupport::getInstance()->real_escape_string($this->sId)."' AS pkg_newsletter_campaign_id
                           FROM `pkg_newsletter_user`
                     INNER JOIN `data_extranet_user` ON `pkg_newsletter_user`.`data_extranet_user_id` = `data_extranet_user`.`id`
                     INNER JOIN `data_extranet_user_data_extranet_group_mlt` ON `data_extranet_user`.`id` = `data_extranet_user_data_extranet_group_mlt`.`source_id`
                          WHERE `data_extranet_user_data_extranet_group_mlt`.`target_id` IN ('".implode("','", $aExtranetGroups)."')
                            AND `pkg_newsletter_user`.`cms_portal_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($oPkgNewsletterGroup->fieldCmsPortalId)."'
                   ";
                MySqlLegacySupport::getInstance()->query($query);
            }
        }
        TCacheManager::PerformeTableChange('pkg_newsletter_queue');
    }

    /**
     * fetches short record data for processing after an ajaxSave
     * is returned by Save method
     * id and name is always available in the returned object
     * overwrite this method to add custom return data.
     *
     * @param array $postData
     *
     * @return TCMSstdClass
     */
    public function GetObjectShortInfo($postData = array())
    {
        $oRecordData = parent::GetObjectShortInfo($postData);

        if (array_key_exists('active', $postData) && '1' == $postData['active']) {
            $oRecordData->message = TGlobal::Translate('chameleon_system_newsletter.text.queue_ready', array('%count%' => $this->iSubscribersAddedToQueue));
        }

        return $oRecordData;
    }

    /**
     * set public methods here that may be called from outside.
     *
     * @return void
     */
    public function DefineInterface()
    {
        parent::DefineInterface();
        $this->methodCallAllowed[] = 'DeleteCampaignQueue';
    }

    /**
     * add table-specific buttons to the editor (add them directly to $this->oMenuItems).
     *
     * @return void
     */
    protected function GetCustomMenuItems()
    {
        if ($this->AllowDeletingCampaignQueue()) {
            $oMenuItem = new TCMSTableEditorMenuItem();
            $oMenuItem->sItemKey = 'DeleteCampaignQueue';
            $oMenuItem->sDisplayName = TGlobal::Translate('chameleon_system_newsletter.action.clear_queue');
            $oMenuItem->sIcon = 'fas fa-user-slash';

            $oGlobal = TGlobal::instance();
            $oExecutingModulePointer = &$oGlobal->GetExecutingModulePointer();

            $aURLData = array('module_fnc' => array($oExecutingModulePointer->sModuleSpotName => 'ExecuteAjaxCall'), '_fnc' => 'DeleteCampaignQueue', '_noModuleFunction' => 'true', 'pagedef' => $oGlobal->GetUserData('pagedef'), 'id' => $this->sId, 'tableid' => $this->oTableConf->id);
            $sURL = PATH_CMS_CONTROLLER.'?'.TTools::GetArrayAsURLForJavascript($aURLData);
            $sJS = "GetAjaxCall('{$sURL}', DisplayAjaxMessage);";
            $oMenuItem->sOnClick = $sJS;
            $this->oMenuItems->AddItem($oMenuItem);
        }
    }

    /**
     * create a number of vouchers in the shop_voucher table.
     *
     * @param string $sCode             - the code to use. if empty, a random unique code will be generated
     * @param int    $iNumberOfVouchers - number of vouchers to create (will fetch this from user input if null given)
     *
     * @return string
     */
    public function DeleteCampaignQueue()
    {
        $sMessage = 'Warteschlange gelöscht';

        if ($this->AllowDeletingCampaignQueue()) {
            $oGlobal = TGlobal::instance();
            $sId = $oGlobal->GetUserData('id');

            $query = "DELETE `pkg_newsletter_queue`.*
                    FROM `pkg_newsletter_queue`
                   WHERE `pkg_newsletter_queue`.`pkg_newsletter_campaign_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sId)."'
                 ";
            $tRes = MySqlLegacySupport::getInstance()->query($query);
            TCacheManager::PerformeTableChange('pkg_newsletter_queue');
            if ($tRes) {
                $sMessage = MySqlLegacySupport::getInstance()->affected_rows().' Warteschlangeneinträge gelöscht';
            } else {
                $sMessage = 'Fehler beim Löschen der Warteschlange';
            }
            $this->SaveFields(array('send_statistics' => '', 'send_start_date' => '0000-00-00 00:00:00', 'send_end_date' => '0000-00-00 00:00:00'));
        }

        return $sMessage;
    }

    /**
     * return true if the current user has the right to create codes in the shop_voucher table.
     *
     * @return bool
     */
    protected function AllowDeletingCampaignQueue()
    {
        $bAllowDeletingCampaignQueue = false;

        $oTargetTableConf = TdbCmsTblConf::GetNewInstance();
        /** @var $oTargetTableConf TdbCmsTblConf */
        if ($oTargetTableConf->Loadfromfield('name', 'pkg_newsletter_campaign')) {
            $oGlobal = TGlobal::instance();
            $bUserIsInCodeTableGroup = $oGlobal->oUser->oAccessManager->user->IsInGroups($oTargetTableConf->fieldCmsUsergroupId);
            $bHasNewPermissionOnTargetTable = ($oGlobal->oUser->oAccessManager->HasNewPermission('pkg_newsletter_campaign'));
            $bAllowDeletingCampaignQueue = ($bUserIsInCodeTableGroup && $bHasNewPermissionOnTargetTable);
        }

        return $bAllowDeletingCampaignQueue;
    }
}
