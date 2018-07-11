<?php
$sSpotName = TGlobal::instance()->GetExecutingModulePointer()->sModuleSpotName;
$sLoadCopy = (isset($bLoadCopy) && '1' == $bLoadCopy) ? ('&bLoadCopy='.$bLoadCopy) : ('');
?>

<script type="text/javascript">
    var sModuleInstanceID = '';
    function LoadCMSInstance(id) {
        sModuleInstanceID = id;
        GetAjaxCallTransparent('<?=PATH_CMS_CONTROLLER; ?>?<?=TTools::GetArrayAsURLForJavascript(array('pagedef' => 'templateengineplain',  'module_fnc' => array($sSpotName => 'ExecuteAjaxCall'), '_fnc' => 'getChooseModuleViewDialog')); ?>&instanceid=' + id+"&id=<?=$data['id']; ?>&spotName=<?=$data['spotname'].$sLoadCopy; ?>", LoadModuleInstanceCallback);
    }

    function LoadModuleInstanceCallback(data) {
       if (data) {
           if (data.bIsTableLocked){
               alert('<?=TGlobal::Translate('chameleon_system_core.template_engine.error_instance_locked'); ?>');
           } else{
               $("#chooseModuleViewDialog").replaceWith(data.html);
               if (data.bOpenDialog){
                   openModuleViewChooseDialog();
               }else{
                   $('#loadmoduleclass').submit();
               }
           }
       } else {
           alert('<?=TGlobal::Translate('chameleon_system_core.template_engine.ajax_error'); ?>');
       }
    }
</script>

<?= $data['sTable']; ?>