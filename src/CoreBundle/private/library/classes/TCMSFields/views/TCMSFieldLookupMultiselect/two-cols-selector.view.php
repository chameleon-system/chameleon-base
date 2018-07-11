<?php
/** @var $oField TCMSField* */
/** @var $bFieldHasErrors boolean* */
/** @var $oConnectedMLTRecords TCMSRecordList* */
/** @var $oMLTRecords TCMSRecordList* */
$aRecordsConnected = $oConnectedMLTRecords->GetIdList();
?>
<script type="text/javascript">
    $(document).ready(function () {
        $("#<?=TGlobal::OutJS($this->name); ?>").attr("name", "<?=TGlobal::OutJS($this->name); ?>tmp");
    });
    function Add<?=TGlobal::OutHTML($this->name); ?>() {
        $("#<?=TGlobal::OutJS($this->name); ?> option:selected").each(function () {
            var value = $(this).val();
            var text = $(this).html();
            $("#<?=TGlobal::OutJS($this->name); ?>" + value).remove();
            var sPrependHTML = "<div id=\"<?=TGlobal::OutJS($this->name); ?>" + value + "\">" + text + " <span class=\"close\" onclick=\"$(this).parent().remove();\"><?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.field_lookup_multi_select.remove')); ?><\/span><input type=\"hidden\" name=\"<?=TGlobal::OutJS($this->name); ?>[]\" value=\"" + value + "\" \/><\/div>";
            $("#<?=TGlobal::OutHTML($this->name); ?>selection").prepend(sPrependHTML);
        });
    }
</script>
<div class="multiselect-left">
    <?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.field_lookup_multi_select.connect_elements')); ?>
    <select name="<?=TGlobal::OutHTML($this->name); ?>[]" multiple size="10" id="<?=TGlobal::OutHTML($this->name); ?>">
        <?php
        while ($oRecord = $oMLTRecords->Next()) {
            $selected = '';
            if (in_array($oRecord->id, $aRecordsConnected)) {
                $selected = 'selected="selected"';
            }
            echo '<option value="'.TGlobal::OutHTML($oRecord->id).'" '.$selected.'>'.TGlobal::OutHTML($oRecord->GetName()).'</option>';
            echo "\n";
        }
        ?>
    </select>
</div>
<div class="multiselect-right">
    <?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.field_lookup_multi_select.connected_elements')); ?>
    <div id="<?=TGlobal::OutHTML($this->name); ?>selection">
        <?php
        $oConnectedMLTRecords->GoToStart();
        while ($oConnectedRecord = $oConnectedMLTRecords->Next()) {
            echo '<div id="'.TGlobal::OutJS($this->name).$oConnectedRecord->id.'">'.$oConnectedRecord->GetName().' <span class="close" onclick="$(this).parent().remove();">'.$this->GetRemoveButtonContent().'</span><input type="hidden" name="'.TGlobal::OutJS($this->name).'[]" value="'.$oConnectedRecord->id.'" /></div>';
        }
        ?>
    </div>
</div>
<div class="cleardiv">&nbsp;</div>
<button
    onclick="Add<?=TGlobal::OutHTML($this->name); ?>();return false;"><?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.field_lookup_multi_select.connect_element')); ?></button>
<div class="cleardiv">&nbsp;</div>