            $oList = null;
            if (is_null($iLanguageId)) $iLanguageId = TGlobal::GetActiveLanguageId();
            $oRecord = <?=TCMSTableToClass::PREFIX_CLASS.TCMSTableToClass::ConvertToClassString($sTableDatabaseName); ?>::GetNewInstance($<?=$iLookupFieldName; ?>);
            $sFilter = "`<?=$sTableDatabaseName; ?>`.`<?=$aFieldData['sFieldDatabaseName']; ?>`= ".\ChameleonSystem\CoreBundle\ServiceLocator::get('database_connection')->quote($<?=$iLookupFieldName; ?>)."
                            AND `<?=$sTableDatabaseName; ?>`.`<?=$aFieldData['sFieldDatabaseName']; ?>_table_name`= ".\ChameleonSystem\CoreBundle\ServiceLocator::get('database_connection')->quote($oRecord->sqlData['<?=$aFieldData['sFieldDatabaseName']; ?>_table_name']);
            $query = <?=$sReturnType; ?>::GetDefaultQuery($iLanguageId, $sFilter);
            $oList =& <?=$sReturnType; ?>::GetList($query);
            return $oList;
