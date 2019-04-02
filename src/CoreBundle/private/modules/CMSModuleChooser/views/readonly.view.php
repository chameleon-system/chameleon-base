<?php

/**
 * @var $oModule \TdbCmsTplModule
 * @var $oModuleInstance \TdbCmsTplModuleInstance
 */

$menuPrefix = TGlobal::OutHTML($data['sModuleSpotName']);
?>
<div style="margin-top: 4px;position:relative;z-index:1000">
    <div style="border-color:#<?=$oModuleInstanceColorState; ?>" class="CMSModuleChooserReadOnly"
         spotname="<?=$menuPrefix; ?>">
        <?php
        if (null !== $oModule) {
            $iconFontCssClass = $oModule->fieldIconFontCssClass;
            if ('' === $iconFontCssClass) {
                $iconFontCssClass = '';
            }
        ?>
        <div id="moduleheaderline_<?=$menuPrefix; ?>" style="background-color: #81A6DD; height: 20px;">
            <div class="cleardiv">&nbsp;</div>
        </div>
        <?php
        $aViewMapping = $oModule->GetViewMapping();
    if (array_key_exists($oModuleInstance->sqlData['template'], $aViewMapping)) {
        $sViewName = $aViewMapping[$oModuleInstance->sqlData['template']];
    } else {
        $sViewName = $oModuleInstance->sqlData['template'];
    } ?>
        <div style="background-color: #63c2de">
            <div class="moduleType">
                <i class="<?=TGlobal::OutHTML($iconFontCssClass)?>"></i> <?=TGlobal::OutHTML($oModule->sqlData['name']); ?>
            </div>
        </div>
        <div class="moduleInfo"><strong><?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.template_engine.module_view')); ?>
            :</strong> <?=TGlobal::OutHTML(str_replace('_', ' ', $sViewName)); ?></div>
        <div class="moduleInfo"><strong><?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.template_engine.slot_content')); ?>
            : </strong><?=TGlobal::OutHTML($oModuleInstance->sqlData['name']); ?></div>
        <?php
} else {
        ?>
        <div style="background-color: #63c2de">
            <div class="moduleType"
                 style="background: url(<?=TGlobal::GetPathTheme(); ?>/images/templateEngineEditor/cross.png); background-repeat: no-repeat; background-position: 3px;"><?php echo TGlobal::Translate('chameleon_system_core.template_engine.slot_is_empty'); ?></div>
        </div>
        <?php
    } ?>
    </div>
</div>
