        <?php
            $aParamString = [];
foreach ($aParameters as $sParamName => $aParamData) {
    $aParamString[] = "\${$sParamName}";
} ?>
$itemList = $this-><?php echo $sParentMethodName; ?>(<?php echo implode(', ', $aParamString); ?>);
        if(true === $this-><?php echo $inverseEmptyFieldName; ?> && 0 === $itemList->Length()) {
            return null;
        }

        return $itemList;
