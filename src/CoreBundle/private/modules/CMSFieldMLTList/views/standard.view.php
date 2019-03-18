<form id="cmsform" name="cmsform" method="post" action="<?=PATH_CMS_CONTROLLER; ?>" accept-charset="UTF-8">
    <input type="hidden" name="tableid" value="<?=TGlobal::OutHTML($data['id']); ?>"/>
    <input type="hidden" name="pagedef" value="tableeditor"/>
    <input type="hidden" name="id" value=""/>
    <input type="hidden" name="module_fnc[contentmodule]" value=""/>
    <?php if (array_key_exists('sRestriction', $data)) {
    ?><input type="hidden" name="sRestriction"
                                                                  value="<?=TGlobal::OutHTML($data['sRestriction']); ?>"/><?php
} ?>
    <?php if (array_key_exists('sRestrictionField', $data)) {
        ?><input type="hidden" name="sRestrictionField"
                                                                       value="<?=TGlobal::OutHTML($data['sRestrictionField']); ?>"/><?php
    } ?>
    <?php if (array_key_exists('_isiniframe', $data)) {
        ?><input type="hidden" name="_isiniframe"
                                                                 value="<?=TGlobal::OutHTML($data['_isiniframe']); ?>"/><?php
    } ?>
</form>
<script>
    function addMLTConnectionPassThrough(id) {
        parent.addMLTConnection('<?php echo str_replace('_mlt', '', $data['sRestrictionField']); ?>', '<?=$data['name']; ?>', '<?php echo $data['sRestriction']; ?>', id);
        toasterMessage('<?php echo TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.field_mlt.item_added')); ?>', 'SUCCESS');
    }
</script>
<?php echo $data['sTable']; ?>
