<?php
/**
 * @var $oField      TCMSFieldModuleInstance
 * @var $oModuleList TdbCmsTplModuleList
 */
$menuPrefix = $oField->name;
?>
<h2>
    <?php
    echo TGlobal::Translate('chameleon_system_core.template_engine.module_view').': ';
    echo '<span id="'.$oField->name.'CurrentView">'.$oField->oTableRow->sqlData[$oField->name.'_view'].'</span>';
    ?>
</h2>
<input type="hidden" name="<?=$oField->name; ?>_view" id="<?=$oField->name; ?>_view" value="<?=$sModuleView; ?>"/>
<script type="text/javascript">
    <?php
    if (!defined('moduleInstanceMenuloaded')) {
        define('moduleInstanceMenuloaded', '1'); ?>
    function ResetModuleInstance(fieldName, defaultValue) {
        $('#' + fieldName).val(defaultValue);
        $('#' + fieldName + '_view').val('');
        document.cmseditform['module_fnc[contentmodule]'].value = 'Save';
        document.cmseditform.submit();
    }

    function Rename(fieldName, instanceName) {
        var sNewName = '';
        var defaultValue = instanceName;
        sNewName = window.prompt('<?=TGlobal::OutJS(TGlobal::Translate('chameleon_system_core.template_engine.prompt_instance_name')); ?>:', defaultValue);
        if (sNewName != null && sNewName != defaultValue) {
            GetAjaxCall('<?=$sAjaxURL; ?>&_fieldName=' + fieldName + '&_fnc=RenameInstance&sName=' + escape(sNewName), RenameFinal);
        }
    }

    function RenameFinal(data, statusText) {
        CloseModalIFrameDialog();
        $('#<?=$oField->name; ?>CurrentSelection').html(data.name);
    }

    function ChangeView(fieldName, sViewName) {
        $('#' + fieldName + '_view').val(sViewName);
        document.cmseditform['module_fnc[contentmodule]'].value = 'Save';
        document.cmseditform.submit();
    }

    function selectModuleInstanceRecord(fieldName, id) {
        $('#' + fieldName).val(id);
        $('#' + fieldName + '_view').val('standard');
        document.cmseditform['module_fnc[contentmodule]'].value = 'Save';
        document.cmseditform.submit();
    }

    function CreateModuleInstance(fieldName, moduleID, sView) {
        $('#cmsModuleMenu').hide('fast');
        sNewName = window.prompt('<?=TGlobal::OutJS(TGlobal::Translate('chameleon_system_core.template_engine.prompt_instance_name')); ?>:', '<?=$sRecordName; ?>');
        GetAjaxCall('<?=$sAjaxURL; ?>&_fieldName=' + fieldName + '&_fnc=CreateNewInstance&moduleID=' + moduleID + '&sName=' + escape(sNewName) + '&sView=' + escape(sView), CreateModuleInstanceFinal);
    }

    function CreateModuleInstanceFinal(data, statusText) {
        $('#' + data.fieldName).val(data.id);
        $('#' + data.fieldName + '_view').val(data.view);
        document.cmseditform['module_fnc[contentmodule]'].value = 'Save';
        document.cmseditform.submit();
    }

    function DeleteModuleInstance(fieldName, moduleInstanceId) {
        if (window.confirm('<?=TGlobal::OutJS(TGlobal::Translate('chameleon_system_core.action.confirm_delete')); ?>')) {
            $('#cmsModuleMenu').hide('fast');
            GetAjaxCall('<?=$sAjaxURL; ?>&_fieldName=' + fieldName + '&_fnc=DeleteInstance&moduleInstanceId=' + moduleInstanceId, DeleteModuleInstanceFinal);
        }
    }

    function DeleteModuleInstanceFinal(data, statusText) {
        CloseModalIFrameDialog();
        if (data && typeof(data) == 'object') {
            var message = '<h1><?=TGlobal::OutJS(TGlobal::Translate('chameleon_system_core.field_module_instance.error')); ?></h1><?=TGlobal::OutJS(TGlobal::Translate('chameleon_system_core.template_engine.error_delete_still_used')); ?><br /><br />';

            for (var i = 0; i < data.length; i++) {
                message += '<div style="padding-bottom: 10px;">';
                message += data[i].tree;
                message += '<a href="<?=$sPageEditURL; ?>&id=' + data[i].id + '" target="_parent" class="actionButton"><?=TGlobal::OutJS(TGlobal::Translate('chameleon_system_core.cms_module_page_tree.action_edit_page')); ?></a><hr width="95%" />';
                message += '</div>';
            }
            CreateModalIFrameDialogFromContent(message);
        } else if (data) {
            $('#' + data).val('0');
            $('#' + data + '_view').val(' ');
            document.cmseditform['module_fnc[contentmodule]'].value = 'Save';
            document.cmseditform.submit();
        }
    }


        $(document).ready(function(){
            var container = '<div id="cmsModuleMenu" style="display: none;">';
            container += '<div class="moduleMenuHeader"><a href="#" onclick="jQuery(\'#cmsModuleMenu\').hide(\'fast\');"><?=TGlobal::OutJS(TGlobal::Translate('chameleon_system_core.action.close')); ?></a></div>';
            container += '<a class="scrolltop" href="#">&nbsp;</a>';
            container += '<div id="menuWrapper">&nbsp;</div>';
            container += '<a href="#" class="scrollbottom">&nbsp;</a>';
            container += '</div>';

            $("body").append(container);

            // Make Draggable
            $('#cmsModuleMenu').draggable({ handle:'div.moduleMenuHeader' });

            $('#cmsModuleMenu a.scrollbottom').click(function () {
                scrollDown();
                return false;
            });

            $('#cmsModuleMenu a.scrolltop').click(function () {
                scrollUp();
                return false;
            });
        });
        <?php
    }
    ?>
        $(document).ready(function(){
            $('#<?=$menuPrefix; ?>NewInstanceButton').click(function (e) {
                var xcoord = e.pageX;
                var ycoord = e.pageY;
                var cssObj = {
                    top:ycoord,
                    left:xcoord
                }
                $('#cmsModuleMenu').css(cssObj);

                $('#menuWrapper').html($('#<?=$menuPrefix; ?>MenuTree').html());

                $('div#cmsModuleMenu').show('fast');
                return false;
            });
        });

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
<div id="<?=$menuPrefix; ?>MenuTree" style="display: none;">
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
                } ?>><span class="menueicon"
                         style="background-image: url(<?=TGlobal::GetStaticURLToWebLib('/images/icons/page_edit.gif'); ?>);"><?=TGlobal::Translate('chameleon_system_core.link.edit'); ?></span></a>
                <ul>
                    <?php while ($oTmpRelatedTable = $oRelatedTables->Next()) {
                    $sJS = " onclick=\"EditTable('".TGlobal::OutJS($oTmpRelatedTable->id)."','".TGlobal::OutJS(urlencode($oField->oModuleInstance->id))."','');return false;\"";
                    echo "<li><a href=\"#\" {$sJS}><span class=\"menueicon\" style=\"background-image: url(".TGlobal::GetStaticURLToWebLib('/images/icons/page_edit.gif').');">'.TGlobal::OutHTML($oTmpRelatedTable->GetName())."</span></a></li>\n";
                } ?>
                </ul>
            </li>
            <?php
        }
        if ($oAccessManager->PermitFunction('cms_template_module_edit')) {
            ?>
            <?php
            if (\count($viewMappings) > 1) {
                ?>
                <li><a href="#;" class="hasChildren" onclick="openMenuLevel(this);return false;"><span class="menueicon"
                                                                                                       style="background-image: url(<?=TGlobal::GetStaticURLToWebLib('/images/icons/layout.png'); ?>);"><?=TGlobal::Translate('chameleon_system_core.template_engine.action_change_template'); ?></span></a>
                    <?php
                    echo "<ul>\n";
                $viewCount = 0;
                foreach ($viewMappings as $internalName => $displayName) {
                    ++$viewCount;
                    $jsFunction = " onClick=\"ChangeView('".TGlobal::OutJS($oField->name)."','".TGlobal::OutJS($internalName)."'); return false;\"";
                    echo "<li><a href=\"#\" {$jsFunction}><span class=\"menueicon\" style=\"background-image: url(".TGlobal::GetStaticURLToWebLib('/images/icons/layout_content.png').');">'.TGlobal::OutHTML($displayName)."</span></a></li>\n";
                }
                echo "</ul>\n"; ?>
                </li>
                <?php
            } ?>
            <li><a href="#"
                   onclick="Rename('<?=TGlobal::OutJS($oField->name); ?>','<?=TGlobal::OutJS($oField->oModuleInstance->sqlData['name']); ?>');return false;"><span
                class="menueicon"
                style="background-image: url(<?=TGlobal::GetStaticURLToWebLib('/images/icons/style.png'); ?>);"><?=TGlobal::Translate('chameleon_system_core.template_engine.slot_rename'); ?></span></a>
            </li>
            <li><a href="#"
                   onclick="DeleteModuleInstance('<?=TGlobal::OutJS($oField->name); ?>','<?=TGlobal::OutJS($oField->oModuleInstance->id); ?>');return false;"><span
                class="menueicon"
                style="background-image: url(<?=TGlobal::GetStaticURLToWebLib('/images/icons/page_cross.gif'); ?>);"><?=TGlobal::Translate('chameleon_system_core.template_engine.action_delete_instance_content'); ?></span></a>
            </li>
            <?php
        }

        $oRelatedTables->GoToStart();
    }
    ?>
    <?php
    if ($oAccessManager->PermitFunction('cms_template_module_edit')) {
        ?>
      <li><a href="#" class="hasChildren" onclick="openMenuLevel(this);return false;"><span class="menueicon"
                                                                                            style="background-image: url(<?=TGlobal::GetStaticURLToWebLib('/images/icons/page_new.gif'); ?>);"><?=TGlobal::Translate('chameleon_system_core.template_engine.action_create_module_instance'); ?></span></a>
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
            <li><a href="#" class="<?=$hasChildren; ?>"<?=$jsFunction; ?>><span class="menueicon"
                                                                            style="background-image: url(<?=TGlobal::GetStaticURLToWebLib('/images/icons/'.$oModule->sqlData['icon_list']); ?>);"><?=TGlobal::OutHTML($oModule->GetName()); ?></span></a>
                <?php
                if (\count($viewMappings) > 0) {
                    echo "<ul>\n";
                    $moduleViewJSFunction = '';
                    foreach ($viewMappings as $internalName => $displayName) {
                        $moduleViewJSFunction = " onclick=\"CreateModuleInstance('".TGlobal::OutJS($oField->name)."','".TGlobal::OutJS($oModule->id)."', '".TGlobal::OutJS($internalName)."');return false;\""; ?>
                        <li><a href="#"<?=$moduleViewJSFunction; ?>><span class="menueicon"
                                                                        style="background-image: url(<?=TGlobal::GetStaticURLToWebLib('/images/icons/layout_content.png'); ?>);"><?=TGlobal::OutHTML($displayName); ?></span></a>
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
