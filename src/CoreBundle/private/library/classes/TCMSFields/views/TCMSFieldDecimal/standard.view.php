<?php
/** @var $oField TCMSField* */
/** @var $bFieldHasErrors boolean* */
$dValue = 0;
if (!empty($oField->data)) {
    $dValue = $oField->data;
}
$dValue = doubleval($dValue);
$sFormatValue = number_format($dValue, $oField->_GetNumberOfDecimals(), ',', '.');
?>
<input type="text" id="<?=TGlobal::OutHTML($oField->name); ?>" name="<?=TGlobal::OutHTML($oField->name); ?>"
       value="<?=TGlobal::OutHTML($sFormatValue); ?>"/>