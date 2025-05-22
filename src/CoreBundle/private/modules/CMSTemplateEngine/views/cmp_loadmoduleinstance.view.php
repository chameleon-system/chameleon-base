<?php
use ChameleonSystem\CoreBundle\ServiceLocator;

$urlUtil = ServiceLocator::get('chameleon_system_core.util.url');
$global = ServiceLocator::get('chameleon_system_core.global');
$sSpotName = $global->GetExecutingModulePointer()->sModuleSpotName;
$sLoadCopy = (isset($bLoadCopy) && '1' === $bLoadCopy) ? ('&bLoadCopy='.$bLoadCopy) : ('');
?>
<script type="text/javascript">
    let sModuleInstanceID = '';

    function LoadCMSInstance(id) {
        sModuleInstanceID = id;
        GetAjaxCallTransparent('<?php echo $urlUtil->getArrayAsUrl(
            [
                'pagedef' => 'templateengine',
                'module_fnc' => [
                    $sSpotName => 'ExecuteAjaxCall',
                ],
                    '_fnc' => 'getChooseModuleViewDialog',
            ], PATH_CMS_CONTROLLER.'?', '&'); ?>&instanceid=' + id + '&id=<?php echo $data['id']; ?>&spotName=<?php echo $data['spotname'].$sLoadCopy; ?>', LoadModuleInstanceCallback);
    }

    function LoadModuleInstanceCallback(data) {
        if (data) {
            if (data.bIsTableLocked) {
                alert('<?php echo ServiceLocator::get('translator')->trans('chameleon_system_core.template_engine.error_instance_locked'); ?>');
            } else {
                const chooseModuleViewDialog = document.getElementById("chooseModuleViewDialog");
                if (chooseModuleViewDialog && data.html) {
                    const parser = new DOMParser();
                    const newDoc = parser.parseFromString(data.html, 'text/html');
                    const newDialog = newDoc.getElementById("chooseModuleViewDialog");

                    if (newDialog) {
                        chooseModuleViewDialog.replaceWith(newDialog);
                    } else {
                        console.warn("Dialog content could not be extracted.");
                    }
                }

                if (data.bOpenDialog) {
                    CreateModalDialogFromContainer('chooseModuleViewDialog');
                } else {
                    const loadModuleForm = document.getElementById("loadmoduleclass");
                    if (loadModuleForm) {
                        loadModuleForm.submit();
                    }
                }
            }
        } else {
            alert('<?php echo ServiceLocator::get('translator')->trans('chameleon_system_core.template_engine.ajax_error'); ?>');
        }
    }
</script>

<?php echo $data['sTable']; ?>