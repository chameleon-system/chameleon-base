<?php

/** @var $oField TCMSField* */
/** @var $bFieldHasErrors boolean* */
$sValue = $oField->_GetFieldValue();
if ('0000-00-00' == $sValue or empty($sValue)) {
    $sValue = '';
} else {
    if (false !== strpos($sValue, '-')) {
        $sValue = date('d.m.Y', strtotime($sValue));
    }
}
echo '<input type="text" id="'.TGlobal::OutHTML($oField->name).'"  name="'.TGlobal::OutHTML($oField->name).'" value="'.TGlobal::OutHTML($sValue)."\" class=\"dateMask\" />\n";
