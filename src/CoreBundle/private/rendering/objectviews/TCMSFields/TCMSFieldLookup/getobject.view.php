            if (empty($this-><?php echo $aFieldData['sFieldName']; ?>)) {
                $oReturn = null;
                return $oReturn;
            }
            $oItem = $this->GetFromInternalCache('oLookup<?php echo $aFieldData['sFieldDatabaseName']; ?>');
            if (is_null($oItem)) {
                $oItem = <?php echo $sClassName; ?>::GetNewInstance($this-><?php echo $aFieldData['sFieldName']; ?>, $this->iLanguageId);
                if ($oItem->sqlData === false) $oItem = null;
                $this->SetInternalCache('oLookup<?php echo $aFieldData['sFieldDatabaseName']; ?>',$oItem);
            }
            return $oItem;
