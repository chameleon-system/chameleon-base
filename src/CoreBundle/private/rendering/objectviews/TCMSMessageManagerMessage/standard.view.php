<?php
/** @var $oMessageObject TdbCmsMessageManagerMessage */
/** @var $oMessageType TdbCmsMessageManagerMessageType */
/** @var $sMessageString string */
/** @var $aCallTimeVars array */
$sStyle = '';
if (!empty($oMessageType->fieldColor)) {
    $sStyle = 'style="color:#'.$oMessageType->fieldColor.'"';
}

if (isset($oMessageType) && is_object($oMessageType) && !empty($sMessageString)) {
    if (!empty($oMessageType->fieldClass)) {
        ?>
    <div class="cmsmessage <?php echo $oMessageType->fieldClass; ?>"><?php echo $sMessageString; ?></div>
    <?php
    } else {
        ?>
    <div class="cmsmessage" <?php echo $sStyle; ?>><?php echo $sMessageString; ?></div>
    <?php
    }
} elseif (!empty($sMessageString)) {
    ?>
<div class="cmsmessage" <?php echo $sStyle; ?>><?php echo $sMessageString; ?></div>
<?php
}
?>
