<?php
/** @var $oField TCMSField* */
/** @var $bFieldHasErrors boolean* */
?>
<textarea id="<?=TGlobal::OutHTML($oField->name); ?>"
          name="<?=TGlobal::OutHTML($oField->name); ?>"><?=TGlobal::OutHTML($oField->data); ?></textarea>