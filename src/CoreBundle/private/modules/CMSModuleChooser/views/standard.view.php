<?php
use ChameleonSystem\CoreBundle\i18n\TranslationConstants;

$menuPrefix = TGlobal::OutHTML($data['sModuleSpotName']);
$translator = \ChameleonSystem\CoreBundle\ServiceLocator::get('translator');

/**
 * @var $oModule          \TdbCmsTplModule
 * @var $oModuleInstance  \TdbCmsTplModuleInstance
 * @var $createModuleMenu string
 */
?>
<div class="moduleChooserMenu">

<div style="margin-top: 4px;position:relative;z-index:1000">
    <div style="border-color:#<?=$oModuleInstanceColorState; ?>" class="CMSModuleChooserTarget" id="CMSModuleChooserTarget<?=$menuPrefix; ?>" data-spotname="<?=$menuPrefix; ?>">
        <div id="moduleheaderline_<?=$menuPrefix; ?>">
            <a id="launch<?=$menuPrefix; ?>" class="cmsModuleMenuLauncher" href="javascript:void(0);"
               onclick="return false;"
               ><i class="fas fa-edit"></i> <?=$translator->trans('chameleon_system_core.template_engine.spot_menu_headline', array(), TranslationConstants::DOMAIN_BACKEND); ?></a>
        </div>
        <?php
        if (null !== $oModule) {
            $iconFontCssClass = $oModule->fieldIconFontCssClass;
            if ('' === $iconFontCssClass) {
                $iconFontCssClass = 'fas fa-pen-square';
            } ?>
        <?php
        $aViewMapping = $oModule->GetViewMapping();
            if (array_key_exists($oModuleInstance->sqlData['template'], $aViewMapping)) {
                $sViewName = $aViewMapping[$oModuleInstance->sqlData['template']];
            } else {
                $sViewName = $oModuleInstance->sqlData['template'];
            } ?>
        <div style="background-color: #63c2de">
            <div class="moduleType">
                <i class="<?=TGlobal::OutHTML($iconFontCssClass); ?>"></i> <?=TGlobal::OutHTML($oModule->sqlData['name']); ?>
            </div>
        </div>
        <div class="moduleInfo"><strong><?=$translator->trans('chameleon_system_core.template_engine.module_view', array(), TranslationConstants::DOMAIN_BACKEND); ?>
            :</strong> <?=TGlobal::OutHTML(str_replace('_', ' ', $sViewName)); ?></div>
        <div class="moduleInfo"><strong><?=$translator->trans('chameleon_system_core.template_engine.slot_content', array(), TranslationConstants::DOMAIN_BACKEND); ?>
            : </strong><?=TGlobal::OutHTML($oModuleInstance->sqlData['name']); ?></div>
        <div style="background-color:#20a8d8; text-align:right;">
            <span style="font-size:10px;color:#FFFFFF;font-weight:bold;"><?= $translator->trans('chameleon_system_core.template_engine.action_move_slot_content', array(), TranslationConstants::DOMAIN_BACKEND); ?></span>

            <div class="CMSModuleChooserCrosshair fas fa-random" data-spotname="<?=TGlobal::OutHTML($data['sModuleSpotName']); ?>"></div>
        </div>
        <?php
        } else {
            ?>
        <div style="background-color: #63c2de">
            <div class="moduleType">
                <i class="fas fa-cube"></i> <?php echo $translator->trans('chameleon_system_core.template_engine.slot_is_empty', array(), TranslationConstants::DOMAIN_BACKEND); ?>
            </div>
        </div>
        <?php
        } ?>
        <div>
            <?php
            $oViews = null;
            $oRelatedTables = null;
            if (!is_null($oModuleInstance) && !is_null($oModule)) {
                $oViews = $oModule->GetViews();
                $aViewMapping = $oModule->GetViewMapping();
                $oRelatedTables = $oModule->GetMLT('cms_tbl_conf_mlt');
            }
            ?>
            <form style="margin:0;padding:0" name="moduleblock<?=$menuPrefix; ?>"
                  method="post" action="<?=URL_WEB_CONTROLLER; ?>" accept-charset="UTF-8">
                <input type="hidden" name="__modulechooser" value="true"/>
                <input type="hidden" name="pagedef" value="<?=TGlobal::OutHTML($data['pagedef']); ?>"/>
                <input type="hidden" name="id" value="<?=TGlobal::OutHTML($data['id']); ?>"/>
                <input type="hidden" name="module_fnc[<?=TGlobal::OutHTML($data['sModuleSpotName']); ?>]" value=""/>
                <?php if (!is_null($oModuleInstance)) {
                ?>
                <input type="hidden" name="moduleid"
                       value="<?=TGlobal::OutHTML($oModuleInstance->sqlData['cms_tpl_module_id']); ?>"/>
                <input type="hidden" name="view"
                       value="<?=TGlobal::OutHTML($oModuleInstance->sqlData['template']); ?>"/>
                <input type="hidden" name="instancename"
                       value="<?=TGlobal::OutHTML($oModuleInstance->sqlData['name']); ?>"/>
                <input type="hidden" name="moduleinstanceid"
                       value="<?=TGlobal::OutHTML($oModuleInstance->sqlData['id']); ?>"/>
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
    if (!is_null($oModuleInstance)) {
        $oRelatedTable = $oRelatedTables->Current();
        if (false !== $oRelatedTable) {
            ?>
            <li><a href="javascript:void(0);"
                <?php
                // there is more than one module edit table so show submenu
                if ($oRelatedTables->Length() > 1) {
                    echo ' class="hasChildren" onclick="openMenuLevel(this);return false"';
                } else { // edit table
                    echo " onclick=\"EditTable('".TGlobal::OutJS($oRelatedTable->id)."','".TGlobal::OutJS(urlencode($oModuleInstance->id))."','');return false;\"";
                } ?>><span class="menueicon"><i class="fas fa-edit"></i> <?=$translator->trans('chameleon_system_core.template_engine.slot_edit', array(), TranslationConstants::DOMAIN_BACKEND); ?></span></a>
                <ul>
                    <?php while ($oTmpRelatedTable = $oRelatedTables->Next()) {
                    $sJS = " onclick=\"EditTable('".TGlobal::OutJS($oTmpRelatedTable->id)."','".TGlobal::OutJS(urlencode($oModuleInstance->id))."','');return false;\"";
                    echo '<li ><a href="javascript:void(0);" style="background-color: #'.TGlobal::OutHTML($oModuleInstance->GetModuleConnectedTableColorEditState($oTmpRelatedTable)).'" '.$sJS.'>
                    <span class="menueicon"><i class="fas fa-edit"></i> '.TGlobal::OutHTML($oTmpRelatedTable->GetName())."</span></a></li>\n";
                } ?>
                </ul>
            </li>
            <?php
        }

        if ($data['oAccessManager']->PermitFunction('cms_template_module_edit')) {
            if ($oViews->Length() > 1 && $data['functionRights']['bInstanceChangeViewAllowed']) {
                ?>
                <li><a href="javascript:void(0);" class="hasChildren" onclick="openMenuLevel(this);return false;"><span
                    class="menueicon"><i class="fas fa-th-large"></i> <?=$translator->trans('chameleon_system_core.template_engine.action_change_template', array(), TranslationConstants::DOMAIN_BACKEND); ?></span></a>
                    <?php
                    echo "<ul>\n";
                $viewCount = 0;
                foreach ($aViewMapping as $sView => $sViewName) {
                    ++$viewCount;
                    $sNameView = TGlobal::OutHTML($sViewName);
                    $jsFunction = " onClick=\"ChangeView('".TGlobal::OutHTML($data['sModuleSpotName'])."', '".TGlobal::OutHTML($sView)."'); return false;\"";
                    if ($sView == $oModuleInstance->sqlData['template']) {
                        $sNameView = '<strong>'.TGlobal::OutHTML($sViewName).'</strong>';
                        $jsFunction = ' onclick="return false"';
                    }
                    echo "<li><a href=\"javascript:void(0);\" {$jsFunction}><span class=\"menueicon\"><i class=\"fas fa-th-large\"></i> ".TGlobal::OutHTML($sViewName)."</span></a></li>\n";
                }
                echo "</ul>\n"; ?>
                </li>
                <?php
            }
            if ($data['functionRights']['bInstanceRenameInstanceAllowed']) {
                ?>
                <li><a href="javascript:void(0);"
                       onclick="Rename('<?=TGlobal::OutHTML($data['sModuleSpotName']); ?>','<?=TGlobal::OutJS($oModuleInstance->sqlData['name']); ?>');return false;"><span
                    class="menueicon"><i class="fas fa-font"></i> <?=$translator->trans('chameleon_system_core.template_engine.slot_rename', array(), TranslationConstants::DOMAIN_BACKEND); ?></span></a>
                </li>
                <?php
            }
            if ($data['functionRights']['bInstanceClearInstanceAllowed']) {
                ?>
                <li><a href="javascript:void(0);"
                       onclick="ClearModuleInstance('<?=TGlobal::OutHTML($data['sModuleSpotName']); ?>');return false;"><span
                    class="menueicon"><i class="fas fa-power-off"></i> <?=$translator->trans('chameleon_system_core.template_engine.slot_reset', array(), TranslationConstants::DOMAIN_BACKEND); ?></span></a>
                </li>
                <?php
            }
            if ($data['functionRights']['bInstanceDeleteInstanceAllowed']) {
                ?>
                <li><a href="javascript:void(0);"
                       onclick="DeleteModuleInstance('<?=TGlobal::OutHTML($data['sModuleSpotName']); ?>');return false;"><span
                    class="menueicon"><i class="fas fa-trash-alt"></i><?=$translator->trans('chameleon_system_core.template_engine.action_delete_instance_content', array(), TranslationConstants::DOMAIN_BACKEND); ?></span></a>
                </li>
                <?php
            }
            if ($data['functionRights']['bInstanceCopyInstanceAllowed']) {
                ?>
                <li><a href="javascript:void(0);"
                       onclick="CopyModuleInstance('<?=TGlobal::OutHTML($data['sModuleSpotName']); ?>');return false;"><span
                    class="menueicon"><i class="fas fa-copy"></i> <?=$translator->trans('chameleon_system_core.template_engine.slot_copy_content', array(), TranslationConstants::DOMAIN_BACKEND); ?></span></a>
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
      <li><a href="javascript:void(0);" class="hasChildren" onclick="openMenuLevel(this);return false;">
              <span class="menueicon"><i class="fas fa-plus-square"></i> <?=$translator->trans('chameleon_system_core.template_engine.action_create_module_instance', array(), TranslationConstants::DOMAIN_BACKEND); ?></span></a>
        <ul>
        <?php
            // modules
        echo $createModuleMenu; ?>
        </ul>
      </li>
            <li>
                <a href="javascript:void(0);" class="hasChildren" onclick="openMenuLevel(this);return false"><span
                    class="menueicon"><i class="fas fa-check-square"></i> <?=$translator->trans('chameleon_system_core.template_engine.slot_load_content_headline', array(), TranslationConstants::DOMAIN_BACKEND); ?></span></a>
                <ul>
                    <li><a href="javascript:void(0);"
                           onclick="LoadModuleInstance('<?=TGlobal::OutHTML($data['sModuleSpotName']); ?>','<?=TGlobal::OutHTML($data['id']); ?>','');return false;"><span
                        class="menueicon"><i class="fas fa-share-alt-square"></i> <?=$translator->trans('chameleon_system_core.template_engine.slot_load_instance', array(), TranslationConstants::DOMAIN_BACKEND); ?></span></a>
                    </li>
                    <li><a href="javascript:void(0);"
                           onclick="LoadModuleInstanceCopy('<?=TGlobal::OutHTML($data['sModuleSpotName']); ?>','<?=TGlobal::OutHTML($data['id']); ?>','');return false;"><span
                        class="menueicon"><i class="fas fa-copy"></i> <?=$translator->trans('chameleon_system_core.template_engine.slot_load_as_copy', array(), TranslationConstants::DOMAIN_BACKEND); ?></span></a>
                    </li>
                </ul>
            </li>
            <?php
        }

        if (!is_null($oModuleInstance) && $data['functionRights']['bInstanceSwitchingAllowed']) {
            ?>
            <li><a href="javascript:void(0);" class="hasChildren" onclick="openMenuLevel(this);return false;"><span
                class="menueicon"><i class="fas fa-random"></i> <?=$translator->trans('chameleon_system_core.template_engine.action_move_slot_content', array(), TranslationConstants::DOMAIN_BACKEND); ?></span></a>
                <ul>
                    <?php
                    // modules
                    /** @var $oModule TdbCMsTplModule */
                    foreach ($data['_oModules']->modules as $sSpotName => $oModule) {
                        $name = $oModule->aModuleConfig['name'];

                        if (!empty($name)) {
                            if (!$oModule->aModuleConfig['static'] && (empty($oModule->aModuleConfig['permittedModules']) || (is_array($oModule->aModuleConfig['permittedModules']) && array_key_exists($oModule->sqlData['classname'], $oModule->aModuleConfig['permittedModules']) && in_array($oModuleInstance->sqlData['template'], $oModule->aModuleConfig['permittedModules'][$oModule->sqlData['classname']])))
                            ) {
                                ?>
                                <li><a href="#" class="moduleInstanceSwitcher" data-sourcespot="<?=$menuPrefix; ?>" data-targetspot="<?=$sSpotName; ?>"><span class="menueicon"><i class="fas fa-th-large"></i> <?=TGlobal::OutHTML($name); ?></span></a>
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
            class="menueicon"><i class="fas fa-tools"></i> <?=$translator->trans('chameleon_system_core.template_engine.slot_edit_definition', array(), TranslationConstants::DOMAIN_BACKEND); ?></span></a>
        </li>
        <?php
    }
    ?>
    </ul>
</div>
</div>

<!-- chameleon modulechooser menu - end -->