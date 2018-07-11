            $oList = null;
            $sLanguageId = TGlobal::GetActiveLanguageId();
            $oActivePage = TCMSActivePage::GetInstance();
            $sQuery = "SELECT * FROM `<?=$sTableDatabaseName; ?>`
            LEFT JOIN `cms_portal_language_matrix` ON `cms_portal_language_matrix`.`record_id` = `<?=$sTableDatabaseName; ?>`.`id`
            WHERE `cms_portal_language_matrix`.`cms_portal_id` = ".$this->getDatabaseConnection()->quote($oActivePage->fieldCmsPortalId)."
            AND `cms_portal_language_matrix`.`cms_tbl_conf_id` = '<?=$sTableConfId; ?>'
            AND `cms_portal_language_matrix`.`cms_language_id` = ".$this->getDatabaseConnection()->quote($sLanguageId);
            $oList = <?=$sReturnType; ?>::GetList($sQuery);
            return $oList;
