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
    <script type="text/javascript">
        /* <![CDATA[ */
        $(document).ready(function () {
            $('#msg_btn_<?=$oMessageObject->id; ?>').click(function () {
                $('#msg_<?=$oMessageObject->id; ?>').fadeOut();
            });
        });
        /* ]]> */
    </script>

    <div class="cmsmessage <?=$oMessageType->fieldClass; ?>" id="msg_<?=$oMessageObject->id; ?>">
        <?=$sMessageString; ?>
        <input type="button" value="Weiter einkaufen" name="btn_close_msg" id="msg_btn_<?=$oMessageObject->id; ?>">
    </div>
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
