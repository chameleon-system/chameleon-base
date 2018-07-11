<?php

/** @var $oField TCMSField* */
$iMaxLength = 255;
if (is_numeric($oField->fieldWidth) && $oField->fieldWidth > 0) {
    $iMaxLength = $oField->fieldWidth;
}
echo '<input type="text" id="'.TGlobal::OutHTML($oField->name).'" name="'.TGlobal::OutHTML($oField->name).'" value="'.$oField->_GetHTMLValue().'" maxlength="'.TGlobal::OutHTML($iMaxLength).'" />';
