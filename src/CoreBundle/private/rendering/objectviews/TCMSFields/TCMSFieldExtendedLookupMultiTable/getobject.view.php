            if (empty($this-><?=$aFieldData['sFieldName']; ?>)) {
                $oReturn = null;
                return $oReturn;
            }
            $oItem = $this->GetFromInternalCache('oLookup<?=$aFieldData['sFieldDatabaseName']; ?>');
            if (is_null($oItem)) {
                $sClassName = $this-><?=$sMethodName?>ObjectType();
                if (!empty($sClassName)) {
                    $oItem = call_user_func(array($sClassName,'GetNewInstance'), $this-><?=$aFieldData['sFieldName']; ?>, $this->iLanguageId);
                    if ($oItem->sqlData === false) $oItem = null;
                    $this->SetInternalCache('oLookup<?=$aFieldData['sFieldDatabaseName']; ?>',$oItem);
                }
            }
            return $oItem;
