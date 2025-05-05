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
<input type="text" id="<?php echo TGlobal::OutHTML($oField->name); ?>" name="<?php echo TGlobal::OutHTML($oField->name); ?>"
       value="<?php echo TGlobal::OutHTML($sFormatValue); ?>"/>