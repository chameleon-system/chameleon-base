            if (empty($this-><?php echo $aFieldData['sFieldName']; ?>)) {
                $oReturn = null;
                return $oReturn;
            }
            <?php
                $sParameters = '';
foreach ($aParameters as $sParameterName => $aParameterData) {
    $sParameters .= '$'.$sParameterName;
}
?>
            $oItem = $this->GetFromInternalCache('oLookup<?php echo $aFieldData['sFieldDatabaseName']; ?>ForObjectType'.md5(serialize(<?php echo $sParameters; ?>)));
            if (is_null($oItem)) {
                if (substr($sExpectedObject, 0, 3) !== 'Tdb') $sExpectedObject = TCMSTableToClass::GetClassName(TCMSTableToClass::PREFIX_CLASS, $sExpectedObject);
                $sClassName = $this-><?php echo substr($sOriginalMethodName, 1); ?>ObjectType();
                if (!empty($sClassName) && $sClassName == $sExpectedObject) {
                    $oItem = call_user_func(array($sClassName,'GetNewInstance'), $this-><?php echo $aFieldData['sFieldName']; ?>, $this->iLanguageId);
                    if ($oItem->sqlData === false) $oItem = null;
                    $this->SetInternalCache('oLookup<?php echo $aFieldData['sFieldDatabaseName']; ?>_for_object_type',$oItem);
                }
            }
            return $oItem;
