    /**
     * <?php echo $aFieldData['sFieldFullName'].' '.$sMethodDescription."\n"; ?>
<?php
foreach ($aMethodDescription as $sLine) {
    echo "\t\t * {$sLine}\n";
}
     ?>
     *
<?php
     $aParamString = [];
     foreach ($aParameters as $sParamName => $aParamData) {
         echo "\t * @param {$aParamData['sType']} \${$sParamName}";
         if (!empty($aParamData['description'])) {
             echo ' - '.$aParamData['description'];
         }
         echo "\n";
         $tmpString = "\${$sParamName}";
         if (!empty($aParamData['default'])) {
             $tmpString .= " = {$aParamData['default']}";
         }
         $aParamString[] = $tmpString;
     }
     ?>
<?php if (!empty($sReturnType)) {
         echo "\t * @return {$sReturnType}\n";
     } ?>
     */
    <?php echo $sVisibility; ?> function <?php echo $sMethodName; ?>(<?php echo implode(', ', $aParamString); ?>)
    {
<?php echo $sMethodCode; ?>
    }
