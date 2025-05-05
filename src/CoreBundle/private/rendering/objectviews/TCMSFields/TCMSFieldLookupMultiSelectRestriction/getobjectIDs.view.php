        <?php
            $aParamString = [];
foreach ($aParameters as $sParamName => $aParamData) {
    $aParamString[] = "\${$sParamName}";
} ?>
$idList = $this-><?php echo $sParentMethodName; ?>(<?php echo implode(', ', $aParamString); ?>);
        if(true === $this-><?php echo $inverseEmptyFieldName; ?> && 0 === count($idList)) {
            return null;
        }

        return $idList;
