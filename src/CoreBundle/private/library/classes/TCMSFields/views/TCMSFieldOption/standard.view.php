<?php
/** @var $oField TCMSField* */
/* @var $bFieldHasErrors boolean* */
/* @var $aOptionNameMapping array* */
/** @var $aOptions array* */
if (count($aOptions) >= 3) { // show as select box?>
<select name="<?php echo TGlobal::OutHTML($oField->name); ?>" id="<?php echo TGlobal::OutHTML($oField->name); ?>">
    <?php if ($oField->EmptySelectionAllowed()) {
        ?>
    <option value=""><?php echo TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.form.select_box_nothing_selected')); ?></option>
    <option value="">-------------------------------------------</option>
    <?php
    } ?>
    <?php
        foreach ($aOptions as $key => $value) {
            $selected = '';
            if ($oField->data == $key || (empty($oField->data) && !$oField->EmptySelectionAllowed() && $oField->oDefinition->sqlData['field_default_value'] == $key)) {
                $selected = ' selected="selected"';
            }
            if (array_key_exists($value, $aOptionNameMapping)) {
                $value = $aOptionNameMapping[$value];
            }
            echo '<option value="'.TGlobal::OutHTML($key)."\"{$selected}>".TGlobal::OutHTML($value)."</option>\n";
        } ?>
</select>
<?php
} else {
    // show as radio button
    $selected = '';
    if ('' == $oField->data) {
        $selected = ' checked="checked"';
    }
    if ($oField->EmptySelectionAllowed()) {
        ?>
    <label style="padding-right: 10px;">
        <input type="radio" class="radio" style="margin-right: 3px;" id="<?php echo TGlobal::OutHTML($oField->name); ?>"
               name="<?php echo TGlobal::OutHTML($oField->name); ?>"
               value=""<?php echo $selected; ?> /> <?php echo ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.field_options.select_nothing'); ?>
    </label>
    <?php
    }
    foreach ($aOptions as $key => $value) {
        $selected = '';
        if ($oField->data == $key || (empty($oField->data) && !$oField->EmptySelectionAllowed() && $oField->oDefinition->sqlData['field_default_value'] == $key)) {
            $selected = ' checked="checked"';
        }
        if (array_key_exists($value, $aOptionNameMapping)) {
            $value = $aOptionNameMapping[$value];
        } ?>
    <label style="padding-right: 10px;">
        <input type="radio" class="radio" style="margin-right: 3px;" id="<?php echo TGlobal::OutHTML($oField->name); ?>"
               name="<?php echo TGlobal::OutHTML($oField->name); ?>"
               value="<?php echo TGlobal::OutHTML($key); ?>"<?php echo $selected; ?> /> <?php echo TGlobal::OutHTML($value); ?>
    </label>
    <?php
    }
}
