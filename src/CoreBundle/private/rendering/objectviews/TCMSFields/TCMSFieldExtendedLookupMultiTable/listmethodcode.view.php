            $oList = null;
            if (null === $iLanguageId) {
                self::getMyLanguageService()->getActiveLanguageId();
            }
            $oRecord = <?php echo TCMSTableToClass::PREFIX_CLASS.TCMSTableToClass::ConvertToClassString($sTableDatabaseName); ?>::GetNewInstance($<?php echo $iLookupFieldName; ?>);
            $sFilter = "`<?php echo $sTableDatabaseName; ?>`.`<?php echo $aFieldData['sFieldDatabaseName']; ?>`= ".\ChameleonSystem\CoreBundle\ServiceLocator::get('database_connection')->quote($<?php echo $iLookupFieldName; ?>)."
                            AND `<?php echo $sTableDatabaseName; ?>`.`<?php echo $aFieldData['sFieldDatabaseName']; ?>_table_name`= ".\ChameleonSystem\CoreBundle\ServiceLocator::get('database_connection')->quote($oRecord->sqlData['<?php echo $aFieldData['sFieldDatabaseName']; ?>_table_name']);
            $query = <?php echo $sReturnType; ?>::GetDefaultQuery($iLanguageId, $sFilter);
            $oList = <?php echo $sReturnType; ?>::GetList($query);
            return $oList;
