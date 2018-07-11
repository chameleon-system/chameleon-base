            if (empty($this-><?=$aFieldData['sFieldName']; ?>)) {
                $oReturn = null;
                return $oReturn;
            }
            $oItem = $this->GetFromInternalCache('oLookup<?=$aFieldData['sFieldDatabaseName']; ?>');
            if (is_null($oItem)) {
                $oItem = <?=$sClassName; ?>::GetNewInstance($this-><?=$aFieldData['sFieldName']; ?>, $this->iLanguageId);
                if ($oItem->sqlData === false) $oItem = null;
                $this->SetInternalCache('oLookup<?=$aFieldData['sFieldDatabaseName']; ?>',$oItem);
            }
            return $oItem;
