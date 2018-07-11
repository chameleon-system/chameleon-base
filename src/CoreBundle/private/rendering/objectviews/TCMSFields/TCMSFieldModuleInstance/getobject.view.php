            if (empty($this-><?=$aFieldData['sFieldName']; ?>)) {
                $oReturn = null;
                return $oReturn;
            }
            $oItem = $this->GetFromInternalCache('oLookup<?=$aFieldData['sFieldDatabaseName']; ?>');
            if (is_null($oItem)) {
                $oItem = <?=$sClassName; ?>::GetNewInstance($this-><?=$aFieldData['sFieldName']; ?>, $this->iLanguageId);
                if ($oItem->sqlData === false) $oItem = null;
                else {
                    if(!empty($this->sqlData['<?=$aFieldData['sFieldDatabaseName']; ?>_view'])) {
                        $oItem->sqlData['template'] = $this->sqlData['<?=$aFieldData['sFieldDatabaseName']; ?>_view']; // overload the instance template
                    }
                }

                $this->SetInternalCache('oLookup<?=$aFieldData['sFieldDatabaseName']; ?>',$oItem);
            }

            return $oItem;
