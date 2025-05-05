<?php
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;

/**
 * @var SecurityHelperAccess $securityHelper
 */
$translator = ServiceLocator::get('translator');

$menuPrefix = $oField->name;
?>
<h2>
    <?php
    echo $translator->trans('chameleon_system_core.template_engine.module_view').': ';
echo '<span id="'.$oField->name.'CurrentView">'.$oField->oTableRow->sqlData[$oField->name.'_view'].'</span>';
?>
</h2>
<input type="hidden" name="<?php echo $oField->name; ?>_view" id="<?php echo $oField->name; ?>_view" value="<?php echo $sModuleView; ?>"/>

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
        var sNewName = window.prompt('<?php echo TGlobal::OutJS($translator->trans('chameleon_system_core.template_engine.prompt_instance_name')); ?>:', instanceName);
        if (sNewName !== null && sNewName !== instanceName) {
            GetAjaxCall('<?php echo $sAjaxURL; ?>&_fieldName=' + fieldName + '&_fnc=RenameInstance&sName=' + encodeURIComponent(sNewName), RenameFinal);
        }
    }

    function RenameFinal(data) {
        CloseModalIFrameDialog();
        document.getElementById('<?php echo $oField->name; ?>CurrentSelection').innerHTML = data.name;
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
        var sNewName = window.prompt('<?php echo TGlobal::OutJS($translator->trans('chameleon_system_core.template_engine.prompt_instance_name')); ?>:', '<?php echo $sRecordName; ?>');
        GetAjaxCall('<?php echo $sAjaxURL; ?>&_fieldName=' + fieldName + '&_fnc=CreateNewInstance&moduleID=' + moduleID + '&sName=' + encodeURIComponent(sNewName) + '&sView=' + encodeURIComponent(sView), CreateModuleInstanceFinal);
    }

    function CreateModuleInstanceFinal(data) {
        document.getElementById(data.fieldName).value = data.id;
        document.getElementById(data.fieldName + '_view').value = data.view;
        document.cmseditform['module_fnc[contentmodule]'].value = 'Save';
        document.cmseditform.submit();
    }

    function DeleteModuleInstance(fieldName, moduleInstanceId) {
        if (confirm('<?php echo TGlobal::OutJS($translator->trans('chameleon_system_core.action.confirm_delete')); ?>')) {
            document.getElementById('cmsModuleMenu').style.display = 'none';
            GetAjaxCall('<?php echo $sAjaxURL; ?>&_fieldName=' + fieldName + '&_fnc=DeleteInstance&moduleInstanceId=' + moduleInstanceId, DeleteModuleInstanceFinal);
        }
    }

    function DeleteModuleInstanceFinal(data) {
        CloseModalIFrameDialog();
        if (data && typeof data === 'object') {
            var message = '<h1><?php echo TGlobal::OutJS($translator->trans('chameleon_system_core.field_module_instance.error')); ?></h1><?php echo TGlobal::OutJS($translator->trans('chameleon_system_core.template_engine.error_delete_still_used')); ?><br /><br />';
            data.forEach(item => {
                message += '<div style="padding-bottom: 10px;">';
                message += item.tree;
                message += '<a href="<?php echo $sPageEditURL; ?>&id=' + item.id + '" target="_parent" class="btn btn-danger"><?php echo TGlobal::OutJS($translator->trans('chameleon_system_core.cms_module_page_tree.action_edit_page')); ?></a><hr width="95%" />';
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
        if (!document.getElementById("cmsModuleMenu")) {
            let container = document.createElement("div");
            container.id = "cmsModuleMenu";
            container.style.display = "none";
            container.innerHTML = `
            <div class="moduleMenuHeader">
                <a href="#" onclick="document.getElementById('cmsModuleMenu').style.display='none'; return false;">
                    <?php echo TGlobal::OutJS($translator->trans('chameleon_system_core.action.close')); ?>
                </a>
            </div>
            <div id="menuWrapper">&nbsp;</div>`;

            document.body.appendChild(container);
        }

        document.getElementById("<?php echo $menuPrefix; ?>NewInstanceButton").addEventListener("click", function (event) {
            var xcoord = event.pageX;
            var ycoord = event.pageY;
            var menu = document.getElementById("cmsModuleMenu");

            menu.style.top = ycoord + "px";
            menu.style.left = xcoord + "px";
            document.getElementById("menuWrapper").innerHTML = document.getElementById("<?php echo $menuPrefix; ?>MenuTree").innerHTML;

            menu.style.display = "block";
            event.preventDefault();
        });
    });
    <?php
}
?>
</script>
<?php
$oViews = null;
$oRelatedTables = null;
if (!is_null($oField->oModuleInstance)) {
    $module = $oField->oModule;
    $oViews = $module->GetViews();
    $viewMappings = $module->GetViewMapping();
    $oRelatedTables = $module->GetMLT('cms_tbl_conf_mlt');
}
?>
<!-- chameleon modulechooser menu -->
<div id="<?php echo $menuPrefix; ?>MenuTree" style="display: none;">
    <ul>
        <?php
        if (!is_null($oField->oModuleInstance)) {
            $oRelatedTable = $oRelatedTables->Current();
            if (false !== $oRelatedTable) {
                ?>
        <li><a href="#"
                <?php
                        // there is more than one module edit table so show submenu
                        if ($oRelatedTables->Length() > 1) {
                            echo ' class="hasChildren" onclick="openMenuLevel(this);return false"';
                        } else { // edit table
                            echo " onclick=\"EditTable('".TGlobal::OutJS($oRelatedTable->id)."','".TGlobal::OutJS($oField->oModuleInstance->id)."','');return false;\"";
                        } ?>><i class="fas fa-edit"></i> <?php echo $translator->trans('chameleon_system_core.link.edit'); ?></a>
            <ul>
                <?php while ($oTmpRelatedTable = $oRelatedTables->Next()) {
                    $sJS = " onclick=\"EditTable('".TGlobal::OutJS($oTmpRelatedTable->id)."','".TGlobal::OutJS(urlencode($oField->oModuleInstance->id))."','');return false;\"";
                    echo "<li><a href=\"#\" {$sJS}><i class=\"fas fa-edit\"></i> ".TGlobal::OutHTML($oTmpRelatedTable->GetName())."</a></li>\n";
                } ?>
            </ul>
        </li>
        <?php
            }
            if ($securityHelper->isGranted('CMS_RIGHT_CMS_TEMPLATE_MODULE_EDIT')) {
                ?>
        <?php
                if (\count($viewMappings) > 1) {
                    ?>
        <li><a href="#;" class="hasChildren" onclick="openMenuLevel(this);return false;"><i class="fas fa-desktop"></i> <?php echo $translator->trans('chameleon_system_core.template_engine.action_change_template'); ?></a>
            <?php
                        echo "<ul>\n";
                    $viewCount = 0;
                    foreach ($viewMappings as $internalName => $displayName) {
                        ++$viewCount;
                        $jsFunction = " onClick=\"ChangeView('".TGlobal::OutJS($oField->name)."','".TGlobal::OutJS($internalName)."'); return false;\"";
                        echo "<li><a href=\"#\" {$jsFunction}><i class=\"fas fa-desktop\"></i> ".TGlobal::OutHTML($displayName)."</a></li>\n";
                    }
                    echo "</ul>\n"; ?>
        </li>
        <?php
                } ?>
        <li><a href="#"
               onclick="Rename('<?php echo TGlobal::OutJS($oField->name); ?>','<?php echo TGlobal::OutJS($oField->oModuleInstance->sqlData['name']); ?>');return false;"><i class="fab fa-fonticons"></i> <?php echo $translator->trans('chameleon_system_core.template_engine.slot_rename'); ?></a>
        </li>
        <li><a href="#"
               onclick="DeleteModuleInstance('<?php echo TGlobal::OutJS($oField->name); ?>','<?php echo TGlobal::OutJS($oField->oModuleInstance->id); ?>');return false;"><i class="fas fa-file-excel"></i> <?php echo $translator->trans('chameleon_system_core.template_engine.action_delete_instance_content'); ?></a>
        </li>
        <?php
            }

            $oRelatedTables->GoToStart();
        }
?>
        <?php
if ($securityHelper->isGranted('CMS_RIGHT_CMS_TEMPLATE_MODULE_EDIT')) {
    ?>
        <li><a href="#" class="hasChildren" onclick="openMenuLevel(this);return false;"><i class="fas fa-plus"></i> <?php echo $translator->trans('chameleon_system_core.template_engine.action_create_module_instance'); ?></a>
            <ul>
                <?php
            while ($oModule = $oModuleList->Next()) {
                $name = $oModule->GetName();
                if (!empty($name)) {
                    $viewMappings = $oModule->GetViewMapping();
                    $jsFunction = ' onclick="return false;"';
                    $hasChildren = '';

                    if (\count($viewMappings) > 0) {
                        $hasChildren = 'hasChildren';
                        $jsFunction = ' onclick="openMenuLevel(this);return false;"';
                    } ?>
                <li><a href="#" class="<?php echo $hasChildren; ?>"<?php echo $jsFunction; ?>><i class="<?php echo TGlobal::OutHTML($oModule->fieldIconFontCssClass); ?>"></i> <?php echo TGlobal::OutHTML($oModule->GetName()); ?></a>
                    <?php
                        if (\count($viewMappings) > 0) {
                            echo "<ul>\n";
                            $moduleViewJSFunction = '';
                            foreach ($viewMappings as $internalName => $displayName) {
                                $moduleViewJSFunction = " onclick=\"CreateModuleInstance('".TGlobal::OutJS($oField->name)."','".TGlobal::OutJS($oModule->id)."', '".TGlobal::OutJS($internalName)."');return false;\""; ?>
                <li><a href="#"<?php echo $moduleViewJSFunction; ?>><i class="fas fa-desktop"></i> <?php echo TGlobal::OutHTML($displayName); ?></a>
                </li>
            <?php
                            }
                            echo "</ul>\n";
                        } ?>
        </li>
        <?php
                }
            } ?>
    </ul>
    </li>
    <?php
}
?>
    </ul>
</div>
<!-- chameleon modulechooser menu - end -->

