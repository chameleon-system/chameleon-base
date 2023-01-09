            $oList = null;
            if (null === $iLanguageId) {
                $iLanguageId = self::getMyLanguageService()->getActiveLanguageId();
            }
            $query = <?=$sReturnType; ?>::GetDefaultQuery($iLanguageId, "`<?=$sTableDatabaseName; ?>`.`<?=$aFieldData['sFieldDatabaseName']; ?>`= ".\ChameleonSystem\CoreBundle\ServiceLocator::get('database_connection')->quote($<?=$iLookupFieldName; ?>));
            $oList = <?=$sReturnType; ?>::GetList($query);
            return $oList;
