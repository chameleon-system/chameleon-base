            $oList = null;
            if (is_null($iLanguageId)) $iLanguageId = TGlobal::GetActiveLanguageId();
            $query = <?=$sReturnType; ?>::GetDefaultQuery($iLanguageId, "`<?=$sTableDatabaseName; ?>`.`<?=$aFieldData['sFieldDatabaseName']; ?>`= ".\ChameleonSystem\CoreBundle\ServiceLocator::get('database_connection')->quote($<?=$iLookupFieldName; ?>));
            $oList =& <?=$sReturnType; ?>::GetList($query);
            return $oList;
