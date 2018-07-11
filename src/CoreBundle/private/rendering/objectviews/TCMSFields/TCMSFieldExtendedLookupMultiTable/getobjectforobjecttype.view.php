            if (empty($this-><?=$aFieldData['sFieldName']; ?>)) {
                $oReturn = null;
                return $oReturn;
            }
            <?php
                $sParameters = '';
                foreach ($aParameters as $sParameterName => $aParameterData) {
                    $sParameters .= '$'.$sParameterName;
                }
            ?>
            $oItem = $this->GetFromInternalCache('oLookup<?=$aFieldData['sFieldDatabaseName']; ?>ForObjectType'.md5(serialize(<?=$sParameters; ?>)));
            if (is_null($oItem)) {
                if (substr($sExpectedObject, 0, 3) !== 'Tdb') $sExpectedObject = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $sExpectedObject);
                $sClassName = $this-><?=substr($sOriginalMethodName, 1); ?>ObjectType();
                if (!empty($sClassName) && $sClassName == $sExpectedObject) {
                    $oItem = call_user_func(array($sClassName,'GetNewInstance'), $this-><?=$aFieldData['sFieldName']; ?>, $this->iLanguageId);
                    if ($oItem->sqlData === false) $oItem = null;
                    $this->SetInternalCache('oLookup<?=$aFieldData['sFieldDatabaseName']; ?>_for_object_type',$oItem);
                }
            }
            return $oItem;
