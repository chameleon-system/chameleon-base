            $oList = null;
            $sLanguageId = self::getMyLanguageService()->getActiveLanguageId();
            $oActivePage = TCMSActivePage::GetInstance();
            $sQuery = "SELECT * FROM `<?php echo $sTableDatabaseName; ?>`
            LEFT JOIN `cms_portal_language_matrix` ON `cms_portal_language_matrix`.`record_id` = `<?php echo $sTableDatabaseName; ?>`.`id`
            WHERE `cms_portal_language_matrix`.`cms_portal_id` = ".$this->getDatabaseConnection()->quote($oActivePage->fieldCmsPortalId)."
            AND `cms_portal_language_matrix`.`cms_tbl_conf_id` = '<?php echo $sTableConfId; ?>'
            AND `cms_portal_language_matrix`.`cms_language_id` = ".$this->getDatabaseConnection()->quote($sLanguageId);
            $oList = <?php echo $sReturnType; ?>::GetList($sQuery);
            return $oList;
