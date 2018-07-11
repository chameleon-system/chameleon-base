        <?php
            $aParamString = array();
            foreach ($aParameters as $sParamName => $aParamData) {
                $aParamString[] = "\${$sParamName}";
            } ?>
$idList = $this-><?=$sParentMethodName; ?>(<?php echo implode(', ', $aParamString); ?>);
        if(true === $this-><?=$inverseEmptyFieldName; ?> && 0 === count($idList)) {
            return null;
        }

        return $idList;
