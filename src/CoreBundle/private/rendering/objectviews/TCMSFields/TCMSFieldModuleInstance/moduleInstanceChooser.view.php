<?php
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;

$menuPrefix = $oField->name;
?>
<h2>
    <?php
    echo ServiceLocator::get('translator')->trans('chameleon_system_core.template_engine.module_view').': ';
    echo '<span id="'.$oField->name.'CurrentView">'.$oField->oTableRow->sqlData[$oField->name.'_view'].'</span>';
    ?>
</h2>
<input type="hidden" name="<?=$oField->name; ?>_view" id="<?=$oField->name; ?>_view" value="<?=$sModuleView; ?>"/>

<script type="text/javascript">
    <?php
    if (!defined('moduleInstanceMenuloaded')) {
    define('moduleInstanceMenuloaded', '1'); ?>

    function ResetModuleInstance(fieldName, defaultValue) {
        document.getElementById(fieldName).value = defaultValue;
        document.getElementById(fieldName + '_view').value = '';
        document.cmseditform['module_fnc[contentmodule]'].value = 'Save';
        document.cmseditform.submit();
    }

    function Rename(fieldName, instanceName) {
        var sNewName = window.prompt('<?=TGlobal::OutJS(ServiceLocator::get('translator')->trans('chameleon_system_core.template_engine.prompt_instance_name')); ?>:', instanceName);
        if (sNewName !== null && sNewName !== instanceName) {
            GetAjaxCall('<?=$sAjaxURL; ?>&_fieldName=' + fieldName + '&_fnc=RenameInstance&sName=' + encodeURIComponent(sNewName), RenameFinal);
        }
    }

    function RenameFinal(data) {
        CloseModalIFrameDialog();
        document.getElementById('<?=$oField->name; ?>CurrentSelection').innerHTML = data.name;
    }

    function ChangeView(fieldName, sViewName) {
        document.getElementById(fieldName + '_view').value = sViewName;
        document.cmseditform['module_fnc[contentmodule]'].value = 'Save';
        document.cmseditform.submit();
    }

    function selectModuleInstanceRecord(fieldName, id) {
        document.getElementById(fieldName).value = id;
        document.getElementById(fieldName + '_view').value = 'standard';
        document.cmseditform['module_fnc[contentmodule]'].value = 'Save';
        document.cmseditform.submit();
    }

    function CreateModuleInstance(fieldName, moduleID, sView) {
        document.getElementById('cmsModuleMenu').style.display = 'none';
        var sNewName = window.prompt('<?=TGlobal::OutJS(ServiceLocator::get('translator')->trans('chameleon_system_core.template_engine.prompt_instance_name')); ?>:', '<?=$sRecordName; ?>');
        GetAjaxCall('<?=$sAjaxURL; ?>&_fieldName=' + fieldName + '&_fnc=CreateNewInstance&moduleID=' + moduleID + '&sName=' + encodeURIComponent(sNewName) + '&sView=' + encodeURIComponent(sView), CreateModuleInstanceFinal);
    }

    function CreateModuleInstanceFinal(data) {
        document.getElementById(data.fieldName).value = data.id;
        document.getElementById(data.fieldName + '_view').value = data.view;
        document.cmseditform['module_fnc[contentmodule]'].value = 'Save';
        document.cmseditform.submit();
    }

    function DeleteModuleInstance(fieldName, moduleInstanceId) {
        if (confirm('<?=TGlobal::OutJS(ServiceLocator::get('translator')->trans('chameleon_system_core.action.confirm_delete')); ?>')) {
            document.getElementById('cmsModuleMenu').style.display = 'none';
            GetAjaxCall('<?=$sAjaxURL; ?>&_fieldName=' + fieldName + '&_fnc=DeleteInstance&moduleInstanceId=' + moduleInstanceId, DeleteModuleInstanceFinal);
        }
    }

    function DeleteModuleInstanceFinal(data) {
        CloseModalIFrameDialog();
        if (data && typeof data === 'object') {
            var message = '<h1><?=TGlobal::OutJS(ServiceLocator::get('translator')->trans('chameleon_system_core.field_module_instance.error')); ?></h1><?=TGlobal::OutJS(ServiceLocator::get('translator')->trans('chameleon_system_core.template_engine.error_delete_still_used')); ?><br /><br />';
            data.forEach(item => {
                message += '<div style="padding-bottom: 10px;">';
                message += item.tree;
                message += '<a href="<?=$sPageEditURL; ?>&id=' + item.id + '" target="_parent" class="btn btn-danger"><?=TGlobal::OutJS(ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_page_tree.action_edit_page')); ?></a><hr width="95%" />';
                message += '</div>';
            });
            CreateModalIFrameDialogFromContent(message);
        } else if (data) {
            document.getElementById(data).value = '0';
            document.getElementById(data + '_view').value = ' ';
            document.cmseditform['module_fnc[contentmodule]'].value = 'Save';
            document.cmseditform.submit();
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        var container = document.createElement("div");
        container.id = "cmsModuleMenu";
        container.style.display = "none";
        container.innerHTML = `
            <div class="moduleMenuHeader">
                <a href="#" onclick="document.getElementById('cmsModuleMenu').style.display='none'; return false;">
                    <?=TGlobal::OutJS(ServiceLocator::get('translator')->trans('chameleon_system_core.action.close')); ?>
                </a>
            </div>
            <div id="menuWrapper">&nbsp;</div>
        `;

        document.body.appendChild(container);

        document.getElementById("<?=$menuPrefix; ?>NewInstanceButton").addEventListener("click", function (event) {
            var xcoord = event.pageX;
            var ycoord = event.pageY;
            var menu = document.getElementById("cmsModuleMenu");

            menu.style.top = ycoord + "px";
            menu.style.left = xcoord + "px";
            document.getElementById("menuWrapper").innerHTML = document.getElementById("<?=$menuPrefix; ?>MenuTree").innerHTML;

            menu.style.display = "block";
            event.preventDefault();
        });
    });
    <?php
    }
    ?>
</script>
