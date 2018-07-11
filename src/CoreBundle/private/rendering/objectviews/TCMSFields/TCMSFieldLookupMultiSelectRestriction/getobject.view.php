        <?php
            $aParamString = array();
            foreach ($aParameters as $sParamName => $aParamData) {
                $aParamString[] = "\${$sParamName}";
            } ?>
$itemList = $this-><?=$sParentMethodName; ?>(<?php echo implode(', ', $aParamString); ?>);
        if(true === $this-><?=$inverseEmptyFieldName; ?> && 0 === $itemList->Length()) {
            return null;
        }

        return $itemList;
