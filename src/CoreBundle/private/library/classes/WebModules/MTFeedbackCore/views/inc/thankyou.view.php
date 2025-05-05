<?php
$oTableRow = $data['oTableRow']; /* @var $oTableRow TCMSRecord */
?>
<div class="ModuleFeedback">
    <?php if (!empty($oTableRow->sqlData['name'])) {
        echo '<h1>'.TGlobal::OutHTML($oTableRow->sqlData['name']).'</h1>';
    } ?>
    <?php echo $oTableRow->GetTextField('done_text'); ?>
</div>