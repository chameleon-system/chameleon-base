    /**
     * <?php echo $aFieldData['sFieldFullName'].' '.$sMethodDescription."\n"; ?>
<?php
foreach ($aMethodDescription as $sLine) {
    echo "      * {$sLine}\n";
}
?>
     *
<?php
$aParamString = array();
foreach ($aParameters as $sParamName => $aParamData) {
    echo "      * @param {$aParamData['sType']} \${$sParamName}";
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
    echo "      * @return {$sReturnType}\n";
} ?>
     */
    <?= $sVisibility; ?> function <?= $sMethodName; ?>(<?php echo implode(', ', $aParamString); ?>) {
<?php echo $sMethodCode; ?>
    }
