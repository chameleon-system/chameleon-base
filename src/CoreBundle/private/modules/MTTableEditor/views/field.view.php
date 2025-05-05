<?php
/**
 * @var $oCMSUser TCMSUser
 * @var $oField   TCMSField
 */
?>
<div>
    <?php
    include __DIR__.'/includes/menuItems.inc.php';
?>
    <form name="cmseditform" id="cmseditform" method="post" action="<?php echo PATH_CMS_CONTROLLER; ?>"
          style="margin 0; padding 0;" accept-charset="UTF-8" onsubmit="CHAMELEON.CORE.showProcessingModal();">
        <input type="hidden" name="pagedef" value="tableeditor"/>
        <input type="hidden" name="tableid" value="<?php echo TGlobal::OutHTML($data['tableid']); ?>"/>
        <input type="hidden" name="id" value="<?php echo TGlobal::OutHTML($data['id']); ?>"/>
        <input type="hidden" name="referer_id" value="<?php echo TGlobal::OutHTML($data['referer_id']); ?>"/>
        <input type="hidden" name="referer_table" value="<?php echo TGlobal::OutHTML($data['referer_table']); ?>"/>
        <input type="hidden" name="_fieldName" value="<?php echo TGlobal::OutHTML($data['_fieldName']); ?>"/>
        <input type="hidden" name="_fnc" value="AjaxSaveField"/>
        <input type="hidden" name="module_fnc[contentmodule]" value="ExecuteAjaxCall"/>
        <?php
    if (null !== $sForeignField) {
        ?>
            <input type="hidden" name="field" value="<?php echo TGlobal::OutHTML($sForeignField); ?>"/>
        <?php
    }

foreach ($data['aHiddenFields'] as $key => $value) {
    ?>
            <input type="hidden" name="<?php echo TGlobal::OutHTML($key); ?>" value="<?php echo TGlobal::OutHTML($value); ?>"/>
        <?php
} ?>
        <?php
echo $oField->GetContent();
?>
    </form>
</div>