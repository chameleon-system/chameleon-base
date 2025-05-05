            if (empty($this-><?php echo $aFieldData['sFieldName']; ?>)) {
                $oReturn = null;
                return $oReturn;
            }
            $oItem = $this->GetFromInternalCache('oLookup<?php echo $aFieldData['sFieldDatabaseName']; ?>');
            if (is_null($oItem)) {
                $sClassName = $this-><?php echo $sMethodName; ?>ObjectType();
                if (!empty($sClassName)) {
                    $oItem = call_user_func(array($sClassName,'GetNewInstance'), $this-><?php echo $aFieldData['sFieldName']; ?>, $this->iLanguageId);
                    if ($oItem->sqlData === false) $oItem = null;
                    $this->SetInternalCache('oLookup<?php echo $aFieldData['sFieldDatabaseName']; ?>',$oItem);
                }
            }
            return $oItem;
