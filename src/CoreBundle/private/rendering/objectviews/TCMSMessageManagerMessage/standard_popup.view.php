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
            $('#msg_btn_<?php echo $oMessageObject->id; ?>').click(function () {
                $('#msg_<?php echo $oMessageObject->id; ?>').fadeOut();
            });
        });
        /* ]]> */
    </script>

    <div class="cmsmessage <?php echo $oMessageType->fieldClass; ?>" id="msg_<?php echo $oMessageObject->id; ?>">
        <?php echo $sMessageString; ?>
        <input type="button" value="Weiter einkaufen" name="btn_close_msg" id="msg_btn_<?php echo $oMessageObject->id; ?>">
    </div>
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
