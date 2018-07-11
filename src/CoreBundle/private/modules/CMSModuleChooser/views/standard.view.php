<?php
use ChameleonSystem\CoreBundle\i18n\TranslationConstants;

$menuPrefix = TGlobal::OutHTML($data['sModuleSpotName']);
$translator = \ChameleonSystem\CoreBundle\ServiceLocator::get('translator');

?>
<div class="moduleChooserMenu">

<div style="margin-top: 4px;position:relative;z-index:1000">
    <div style="border-color:#<?=$oModuleInstanceColorState; ?>" class="CMSModuleChooserTarget" id="CMSModuleChooserTarget<?=$menuPrefix; ?>" data-spotname="<?=$menuPrefix; ?>">
        <div id="moduleheaderline_<?=$menuPrefix; ?>">
            <a id="launch<?=$menuPrefix; ?>" class="cmsModuleMenuLauncher" href="javascript:void(0);"
               onclick="return false;"
               style="display: block; font-weight: bold; color: #fff; background: #81A6DD url(<?=TGlobal::GetStaticURLToWebLib('/images/icons/layout_edit.png'); ?>) no-repeat scroll 5px center"><span><?=$translator->trans('chameleon_system_core.template_engine.spot_menu_headline', array(), TranslationConstants::DOMAIN_BACKEND); ?></span></a>
        </div>
        <?php if (!is_null($data['oModule'])) {
    ?>
        <?php
        $aViewMapping = $data['oModule']->GetViewMapping();
    if (array_key_exists($data['oModuleInstance']->sqlData['template'], $aViewMapping)) {
        $sViewName = $aViewMapping[$data['oModuleInstance']->sqlData['template']];
    } else {
        $sViewName = $data['oModuleInstance']->sqlData['template'];
    } ?>
        <div style="background-image: url(/chameleon/blackbox/images/header_bg.gif);">
            <div class="moduleType"
                 style="background: url(<?=TGlobal::GetStaticURLToWebLib('/images/icons/'.TGlobal::OutHTML($data['oModule']->sqlData['icon_list'])); ?>); background-repeat: no-repeat; background-position: 3px;"><?=TGlobal::OutHTML($data['oModule']->sqlData['name']); ?></div>
        </div>
        <div class="moduleInfo"><strong><?=$translator->trans('chameleon_system_core.template_engine.module_view', array(), TranslationConstants::DOMAIN_BACKEND); ?>
            :</strong> <?=TGlobal::OutHTML(str_replace('_', ' ', $sViewName)); ?></div>
        <div class="moduleInfo"><strong><?=$translator->trans('chameleon_system_core.template_engine.slot_content', array(), TranslationConstants::DOMAIN_BACKEND); ?>
            : </strong><?=TGlobal::OutHTML($data['oModuleInstance']->sqlData['name']); ?></div>
        <div style="background-color:#81A6DD;text-align:right;">
            <span style="font-size:10px;color:#FFFFFF;font-weight:bold;"><?= $translator->trans('chameleon_system_core.template_engine.action_move_slot_content', array(), TranslationConstants::DOMAIN_BACKEND); ?></span>

            <div class="CMSModuleChooserCrosshair" data-spotname="<?=TGlobal::OutHTML($data['sModuleSpotName']); ?>"
                 style="background: url(<?=TGlobal::GetStaticURLToWebLib('/images/icons/arrow_right.png'); ?>);background-repeat: no-repeat; background-position: right center;float:right;background-color:transparent;height:15px;width:20px;cursor:move">
                &nbsp;</div>
        </div>
        <?php
} else {
        ?>
        <div style="background-image: url(/chameleon/blackbox/images/header_bg.gif);">
            <div class="moduleType"
                 style="background: url(<?=TGlobal::GetPathTheme(); ?>/images/icons/cross.png); background-repeat: no-repeat; background-position: 3px;"><?php echo $translator->trans('chameleon_system_core.template_engine.slot_is_empty', array(), TranslationConstants::DOMAIN_BACKEND); ?></div>
        </div>
        <?php
    } ?>
        <div>
            <?php
            $oViews = null;
            $oRelatedTables = null;
            if (!is_null($data['oModuleInstance']) && !is_null($data['oModule'])) {
                $oViews = $data['oModule']->GetViews();
                $aViewMapping = $data['oModule']->GetViewMapping();
                $oRelatedTables = $data['oModule']->GetMLT('cms_tbl_conf_mlt');
            }
            ?>
            <form style="margin:0;padding:0px" name="moduleblock<?=$menuPrefix; ?>"
                  method="post" action="<?=URL_WEB_CONTROLLER; ?>" accept-charset="UTF-8">
                <input type="hidden" name="__modulechooser" value="true"/>
                <input type="hidden" name="pagedef" value="<?=TGlobal::OutHTML($data['pagedef']); ?>"/>
                <input type="hidden" name="id" value="<?=TGlobal::OutHTML($data['id']); ?>"/>
                <input type="hidden" name="module_fnc[<?=TGlobal::OutHTML($data['sModuleSpotName']); ?>]" value=""/>
                <?php if (!is_null($data['oModuleInstance'])) {
                ?>
                <input type="hidden" name="moduleid"
                       value="<?=TGlobal::OutHTML($data['oModuleInstance']->sqlData['cms_tpl_module_id']); ?>"/>
                <input type="hidden" name="view"
                       value="<?=TGlobal::OutHTML($data['oModuleInstance']->sqlData['template']); ?>"/>
                <input type="hidden" name="instancename"
                       value="<?=TGlobal::OutHTML($data['oModuleInstance']->sqlData['name']); ?>"/>
                <input type="hidden" name="moduleinstanceid"
                       value="<?=TGlobal::OutHTML($data['oModuleInstance']->sqlData['id']); ?>"/>
                <?php
            } else {
                ?>
                <input type="hidden" name="moduleid" value=""/>
                <input type="hidden" name="view" value=""/>
                <?php
            } ?>
                <?php
                $oGlobal = TGlobal::instance();
                if ($oGlobal->UserDataExists('esdisablelinks')) {
                    echo '<input type="hidden" name="esdisablelinks" value="'.TGlobal::OutHTML($oGlobal->GetUserData('esdisablelinks')).'" />';
                }
                if ($oGlobal->UserDataExists('esdisablefrontendjs')) {
                    echo '<input type="hidden" name="esdisablefrontendjs" value="'.TGlobal::OutHTML($oGlobal->GetUserData('esdisablefrontendjs')).'" />';
                }
                if ($oGlobal->UserDataExists('__previewmode')) {
                    echo '<input type="hidden" name="__previewmode" value="'.TGlobal::OutHTML($oGlobal->GetUserData('__previewmode')).'" />';
                }
                if ($oGlobal->UserDataExists('previewLanguageId')) {
                    echo '<input type="hidden" name="previewLanguageId" value="'.TGlobal::OutHTML($oGlobal->GetUserData('previewLanguageId')).'" />';
                }
                ?>
            </form>
        </div>
    </div>
</div>

<!-- chameleon modulechooser menu -->
<div id="<?=$menuPrefix; ?>MenuTree" class="moduleChooserMenuTree" style="display: none;">
<ul>
    <?php

    if (!is_null($data['oModuleInstance'])) {
        $oRelatedTable = $oRelatedTables->Current();
        if (false !== $oRelatedTable) {
            ?>
            <li><a href="javascript:void(0);"
                <?php
                // there is more than one module edit table so show submenu
                if ($oRelatedTables->Length() > 1) {
                    echo ' class="hasChildren" onclick="openMenuLevel(this);return false"';
                } else { // edit table
                    echo " onclick=\"EditTable('".TGlobal::OutJS($oRelatedTable->id)."','".TGlobal::OutJS(urlencode($data['oModuleInstance']->id))."','');return false;\"";
                } ?>><span class="menueicon"
                         style="background-image: url(<?=TGlobal::GetStaticURLToWebLib('/images/icons/page_edit.gif'); ?>);"><?=$translator->trans('chameleon_system_core.template_engine.slot_edit', array(), TranslationConstants::DOMAIN_BACKEND); ?></span></a>
                <ul>
                    <?php while ($oTmpRelatedTable = $oRelatedTables->Next()) {
                    $sJS = " onclick=\"EditTable('".TGlobal::OutJS($oTmpRelatedTable->id)."','".TGlobal::OutJS(urlencode($data['oModuleInstance']->id))."','');return false;\"";
                    echo "<li ><a href=\"javascript:void(0);\" style=\"background-color:#{$oModuleInstance->GetModuleConnectedTableColorEditState($oTmpRelatedTable)}\" {$sJS}><span class=\"menueicon\" style=\"background-image: url(".TGlobal::GetStaticURLToWebLib('/images/icons/page_edit.gif').');">'.TGlobal::OutHTML($oTmpRelatedTable->GetName())."</span></a></li>\n";
                } ?>
                </ul>
            </li>
            <?php
        }

        if ($data['oAccessManager']->PermitFunction('cms_template_module_edit')) {
            if ($oViews->Length() > 1 && $data['functionRights']['bInstanceChangeViewAllowed']) {
                ?>
                <li><a href="javascript:void(0);" class="hasChildren" onclick="openMenuLevel(this);return false;"><span
                    class="menueicon"
                    style="background-image: url(<?=TGlobal::GetStaticURLToWebLib('/images/icons/layout.png'); ?>);"><?=$translator->trans('chameleon_system_core.template_engine.action_change_template', array(), TranslationConstants::DOMAIN_BACKEND); ?></span></a>
                    <?php
                    echo "<ul>\n";
                $viewCount = 0;
                foreach ($aViewMapping as $sView => $sViewName) {
                    ++$viewCount;
                    $sNameView = TGlobal::OutHTML($sViewName);
                    $jsFunction = " onClick=\"ChangeView('".TGlobal::OutHTML($data['sModuleSpotName'])."', '".TGlobal::OutHTML($sView)."'); return false;\"";
                    if ($sView == $data['oModuleInstance']->sqlData['template']) {
                        $sNameView = '<strong>'.TGlobal::OutHTML($sViewName).'</strong>';
                        $jsFunction = ' onclick="return false"';
                    }
                    echo "<li><a href=\"javascript:void(0);\" {$jsFunction}><span class=\"menueicon\" style=\"background-image: url(".TGlobal::GetStaticURLToWebLib('/images/icons/layout_content.png').');">'.TGlobal::OutHTML($sViewName)."</span></a></li>\n";
                }
                echo "</ul>\n"; ?>
                </li>
                <?php
            }
            if ($data['functionRights']['bInstanceRenameInstanceAllowed']) {
                ?>
                <li><a href="javascript:void(0);"
                       onclick="Rename('<?=TGlobal::OutHTML($data['sModuleSpotName']); ?>','<?=TGlobal::OutJS($data['oModuleInstance']->sqlData['name']); ?>');return false;"><span
                    class="menueicon"
                    style="background-image: url(<?=TGlobal::GetStaticURLToWebLib('/images/icons/style.png'); ?>);"><?=$translator->trans('chameleon_system_core.template_engine.slot_rename', array(), TranslationConstants::DOMAIN_BACKEND); ?></span></a>
                </li>
                <?php
            }
            if ($data['functionRights']['bInstanceClearInstanceAllowed']) {
                ?>
                <li><a href="javascript:void(0);"
                       onclick="ClearModuleInstance('<?=TGlobal::OutHTML($data['sModuleSpotName']); ?>');return false;"><span
                    class="menueicon"
                    style="background-image: url(<?=TGlobal::GetStaticURLToWebLib('/images/icons/arrow_undo.png'); ?>);"><?=$translator->trans('chameleon_system_core.template_engine.slot_reset', array(), TranslationConstants::DOMAIN_BACKEND); ?></span></a>
                </li>
                <?php
            }
            if ($data['functionRights']['bInstanceDeleteInstanceAllowed']) {
                ?>
                <li><a href="javascript:void(0);"
                       onclick="DeleteModuleInstance('<?=TGlobal::OutHTML($data['sModuleSpotName']); ?>');return false;"><span
                    class="menueicon"
                    style="background-image: url(<?=TGlobal::GetStaticURLToWebLib('/images/icons/page_cross.gif'); ?>);"><?=$translator->trans('chameleon_system_core.template_engine.action_delete_instance_content', array(), TranslationConstants::DOMAIN_BACKEND); ?></span></a>
                </li>
                <?php
            }
            if ($data['functionRights']['bInstanceCopyInstanceAllowed']) {
                ?>
                <li><a href="javascript:void(0);"
                       onclick="CopyModuleInstance('<?=TGlobal::OutHTML($data['sModuleSpotName']); ?>');return false;"><span
                    class="menueicon"
                    style="background-image: url(<?=TGlobal::GetStaticURLToWebLib('/images/icons/page_copy.png'); ?>);"><?=$translator->trans('chameleon_system_core.template_engine.slot_copy_content', array(), TranslationConstants::DOMAIN_BACKEND); ?></span></a>
                </li>
                <?php
            }
        }

        $oRelatedTables->GoToStart();
    }
    ?>
    <?php
    if ($data['oAccessManager']->PermitFunction('cms_template_module_edit')) {
        if ($data['functionRights']['bInstanceNewInstanceAllowed']) {
            ?>
      <li><a href="javascript:void(0);" class="hasChildren" onclick="openMenuLevel(this);return false;"><span
                class="menueicon"
                style="background-image: url(<?=TGlobal::GetStaticURLToWebLib('/images/icons/page_new.gif'); ?>);"><?=$translator->trans('chameleon_system_core.template_engine.action_create_module_instance', array(), TranslationConstants::DOMAIN_BACKEND); ?></span></a>
        <ul>
        <?php
            // modules
        echo $createModuleMenu; ?>
        </ul>
      </li>
            <li>
                <a href="javascript:void(0);" class="hasChildren" onclick="openMenuLevel(this);return false"><span
                    class="menueicon"
                    style="background-image: url(<?=TGlobal::GetStaticURLToWebLib('/images/icons/page_tick.gif'); ?>);"><?=$translator->trans('chameleon_system_core.template_engine.slot_load_content_headline', array(), TranslationConstants::DOMAIN_BACKEND); ?></span></a>
                <ul>
                    <li><a href="javascript:void(0);"
                           onclick="LoadModuleInstance('<?=TGlobal::OutHTML($data['sModuleSpotName']); ?>','<?=TGlobal::OutHTML($data['id']); ?>','');return false;"><span
                        class="menueicon"
                        style="background-image: url(<?=TGlobal::GetStaticURLToWebLib('/images/icons/page_tick.gif'); ?>);"><?=$translator->trans('chameleon_system_core.template_engine.slot_load_instance', array(), TranslationConstants::DOMAIN_BACKEND); ?></span></a>
                    </li>
                    <li><a href="javascript:void(0);"
                           onclick="LoadModuleInstanceCopy('<?=TGlobal::OutHTML($data['sModuleSpotName']); ?>','<?=TGlobal::OutHTML($data['id']); ?>','');return false;"><span
                        class="menueicon"
                        style="background-image: url(<?=TGlobal::GetStaticURLToWebLib('/images/icons/page_tick.gif'); ?>);"><?=$translator->trans('chameleon_system_core.template_engine.slot_load_as_copy', array(), TranslationConstants::DOMAIN_BACKEND); ?></span></a>
                    </li>
                </ul>
            </li>
            <?php
        }

        if (!is_null($data['oModuleInstance']) && $data['functionRights']['bInstanceSwitchingAllowed']) {
            ?>
            <li><a href="javascript:void(0);" class="hasChildren" onclick="openMenuLevel(this);return false;"><span
                class="menueicon"
                style="background-image: url(<?=TGlobal::GetStaticURLToWebLib('/images/icons/page_new.gif'); ?>);"><?=$translator->trans('chameleon_system_core.template_engine.action_move_slot_content', array(), TranslationConstants::DOMAIN_BACKEND); ?></span></a>
                <ul>
                    <?php
                    // modules
                    /** @var $oModule TdbCMsTplModule */
                    foreach ($data['_oModules']->modules as $sSpotName => $oModule) {
                        $name = $oModule->aModuleConfig['name'];

                        if (!empty($name)) {
                            if (!$oModule->aModuleConfig['static'] && (empty($oModule->aModuleConfig['permittedModules']) || (is_array($oModule->aModuleConfig['permittedModules']) && array_key_exists($data['oModule']->sqlData['classname'], $oModule->aModuleConfig['permittedModules']) && in_array($oModuleInstance->sqlData['template'], $oModule->aModuleConfig['permittedModules'][$data['oModule']->sqlData['classname']])))
                            ) {
                                ?>
                                <li><a href="#" class="moduleInstanceSwitcher" data-sourcespot="<?=$menuPrefix; ?>" data-targetspot="<?=$sSpotName; ?>"><span class="menueicon"
                                                                                                  style="background-image: url(<?=TGlobal::GetStaticURLToWebLib('/images/icons/layout_content.png'); ?>);"><?=TGlobal::OutHTML($name); ?></span></a>
                                </li>
                                <?php
                            }
                        }
                    } ?>
                </ul>
            </li>
            <?php
        }
    }
    if ($data['oAccessManager']->HasEditPermission('cms_master_pagedef_spot')) {
        /** @ $oGlobal TGlobal*/
        $oGlobal = TGlobal::instance();
        $sPageId = $oGlobal->GetUserData('pagedef');
        $oPagedef = TCMSPagedef::GetCachedInstance($sPageId);
        $iCmsMasterPagedefSpotTableID = TTools::GetCMSTableId('cms_master_pagedef_spot');
        $oCmsMasterPageDefSpot = TdbCmsMasterPagedefSpot::GetNewInstance();
        $oCmsMasterPageDefSpot->LoadFromFieldsWithCaching(array('name' => $_moduleID, 'cms_master_pagedef_id' => $oPagedef->sqlData['cms_master_pagedef_id'])); ?>
        <li><a
            onclick="EditCmsMasterSpot('<?=PATH_CMS_CONTROLLER; ?>','<?=$iCmsMasterPagedefSpotTableID; ?>','<?=$oCmsMasterPageDefSpot->id; ?>');"><span
            class="menueicon"
            style="background-image: url(<?=TGlobal::GetStaticURLToWebLib('/images/icons/page_tick.gif'); ?>);"><?=$translator->trans('chameleon_system_core.template_engine.slot_edit_definition', array(), TranslationConstants::DOMAIN_BACKEND); ?></span></a>
        </li>
        <?php
    }
    ?>
    </ul>
</div>
</div>

<!-- chameleon modulechooser menu - end -->