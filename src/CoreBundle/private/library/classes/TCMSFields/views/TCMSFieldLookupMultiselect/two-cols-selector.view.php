<?php
/** @var $oField TCMSField* */
/** @var $bFieldHasErrors boolean* */
/** @var $oConnectedMLTRecords TCMSRecordList* */
/** @var $oMLTRecords TCMSRecordList* */
$aRecordsConnected = $oConnectedMLTRecords->GetIdList();
?>
<script type="text/javascript">
    $(document).ready(function () {
        $("#<?php echo TGlobal::OutJS($this->name); ?>").attr("name", "<?php echo TGlobal::OutJS($this->name); ?>tmp");
    });
    function Add<?php echo TGlobal::OutHTML($this->name); ?>() {
        $("#<?php echo TGlobal::OutJS($this->name); ?> option:selected").each(function () {
            var value = $(this).val();
            var text = $(this).html();
            $("#<?php echo TGlobal::OutJS($this->name); ?>" + value).remove();
            var sPrependHTML = "<div id=\"<?php echo TGlobal::OutJS($this->name); ?>" + value + "\">" + text + " <span class=\"close\" onclick=\"$(this).parent().remove();\"><?php echo TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.field_lookup_multi_select.remove')); ?><\/span><input type=\"hidden\" name=\"<?php echo TGlobal::OutJS($this->name); ?>[]\" value=\"" + value + "\" \/><\/div>";
            $("#<?php echo TGlobal::OutHTML($this->name); ?>selection").prepend(sPrependHTML);
        });
    }
</script>
<div class="multiselect-left">
    <?php echo TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.field_lookup_multi_select.connect_elements')); ?>
    <select name="<?php echo TGlobal::OutHTML($this->name); ?>[]" multiple size="10" id="<?php echo TGlobal::OutHTML($this->name); ?>">
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
    <?php echo TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.field_lookup_multi_select.connected_elements')); ?>
    <div id="<?php echo TGlobal::OutHTML($this->name); ?>selection">
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
    onclick="Add<?php echo TGlobal::OutHTML($this->name); ?>();return false;"><?php echo TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.field_lookup_multi_select.connect_element')); ?></button>
<div class="cleardiv">&nbsp;</div>