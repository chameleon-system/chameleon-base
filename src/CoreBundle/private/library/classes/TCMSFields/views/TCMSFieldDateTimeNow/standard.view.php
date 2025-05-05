<?php
/** @var $oField TCMSField* */
/** @var $bFieldHasErrors boolean* */
$sValue = $oField->_GetFieldValue();
if ('0000-00-00 00:00:00' == $sValue or empty($sValue)) {
    $sValueDate = date('d.m.Y');
    $sValueHour = date('H');
    $sValueMinute = date('i');
} else {
    $sValueDate = date('d.m.Y', strtotime($sValue));
    $sValueHour = date('H', strtotime($sValue));
    $sValueMinute = date('i', strtotime($sValue));
}
?>
<input type="text" id="<?php echo TGlobal::OutHTML($oField->name); ?>_date" name="<?php echo TGlobal::OutHTML($oField->name); ?>_date"
       value="<?php echo TGlobal::OutHTML($sValueDate); ?>"/>
<select name="<?php echo TGlobal::OutHTML($oField->name); ?>_hour" id="<?php echo TGlobal::OutHTML($oField->name); ?>_hour">
    <option value="">&nbsp;&nbsp;</option>
    <?php
    for ($i = 0; $i <= 23; ++$i) {
        $iValue = str_pad($i, 2, '0', STR_PAD_LEFT);
        $sSelected = '';
        if ($iValue == $sValueHour) {
            $sSelected = ' selected="selected"';
        }
        echo '<option value="'.$iValue.'"'.$sSelected.'>'.$iValue.'</option>';
    }
?>
</select>
<select name="<?php echo TGlobal::OutHTML($oField->name); ?>_min" id="<?php echo TGlobal::OutHTML($oField->name); ?>_min">
    <option value="">&nbsp;&nbsp;</option>
    <?php
for ($i = 0; $i <= 59; ++$i) {
    $iValue = str_pad($i, 2, '0', STR_PAD_LEFT);
    $sSelected = '';
    if ($iValue == $sValueMinute) {
        $sSelected = ' selected="selected"';
    }
    echo '<option value="'.$iValue.'"'.$sSelected.'>'.$iValue.'</option>';
}
?>
</select>