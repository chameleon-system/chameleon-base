            if (empty($this-><?php echo $aFieldData['sFieldName']; ?>)) {
                $oReturn = null;
                return $oReturn;
            }
            $oItem = $this->GetFromInternalCache('oLookup<?php echo $aFieldData['sFieldDatabaseName']; ?>');
            if (is_null($oItem)) {
                $oItem = <?php echo $sClassName; ?>::GetNewInstance($this-><?php echo $aFieldData['sFieldName']; ?>, $this->iLanguageId);
                if ($oItem->sqlData === false) $oItem = null;
                else {
                    if(!empty($this->sqlData['<?php echo $aFieldData['sFieldDatabaseName']; ?>_view'])) {
                        $oItem->sqlData['template'] = $this->sqlData['<?php echo $aFieldData['sFieldDatabaseName']; ?>_view']; // overload the instance template
                    }
                }

                $this->SetInternalCache('oLookup<?php echo $aFieldData['sFieldDatabaseName']; ?>',$oItem);
            }

            return $oItem;
