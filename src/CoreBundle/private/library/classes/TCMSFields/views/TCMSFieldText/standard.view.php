<?php
/** @var $oField TCMSField* */
/* @var $bFieldHasErrors boolean* */
?>
<textarea id="<?php echo TGlobal::OutHTML($oField->name); ?>"
          name="<?php echo TGlobal::OutHTML($oField->name); ?>"><?php echo TGlobal::OutHTML($oField->data); ?></textarea>