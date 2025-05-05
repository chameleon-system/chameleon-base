            $oList = null;
            if (null === $iLanguageId) {
                $iLanguageId = self::getMyLanguageService()->getActiveLanguageId();
            }
            $query = <?php echo $sReturnType; ?>::GetDefaultQuery($iLanguageId, "`<?php echo $sTableDatabaseName; ?>`.`<?php echo $aFieldData['sFieldDatabaseName']; ?>`= ".\ChameleonSystem\CoreBundle\ServiceLocator::get('database_connection')->quote($<?php echo $iLookupFieldName; ?>));
            $oList = <?php echo $sReturnType; ?>::GetList($query);
            return $oList;
