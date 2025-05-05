<?php
/** @var $oField TCMSField* */
/* @var $bFieldHasErrors boolean* */
/* @var $aOptions array* */
?>
<select name="<?php echo TGlobal::OutHTML($oField->name); ?>" id="<?php echo TGlobal::OutHTML($oField->name); ?>">
    <?php if ($oField->allowEmptySelection) {
        ?>
    <option value=""><?php echo TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.form.select_box_nothing_selected')); ?></option>
    <?php
    } ?>
    <?php
        foreach ($aOptions as $key => $value) {
            $selected = '';
            if (0 == strcmp($oField->data, $key)) {
                $selected = 'selected="selected"';
            }
            echo '<option value="'.TGlobal::OutHTML($key).'" '.$selected.'>'.TGlobal::OutHTML($value).'</option>';
            echo "\n";
        }
?>
</select>