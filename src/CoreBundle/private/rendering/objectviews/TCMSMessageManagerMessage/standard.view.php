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
    <div class="cmsmessage <?=$oMessageType->fieldClass; ?>"><?=$sMessageString; ?></div>
    <?php
    } else {
        ?>
    <div class="cmsmessage" <?=$sStyle; ?>><?=$sMessageString; ?></div>
    <?php
    }
} elseif (!empty($sMessageString)) {
    ?>
<div class="cmsmessage" <?=$sStyle; ?>><?=$sMessageString; ?></div>
<?php
}
?>
