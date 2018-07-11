<?php
$menuPrefix = TGlobal::OutHTML($data['sModuleSpotName']);
?>
<div style="margin-top: 4px;position:relative;z-index:1000">
    <div style="border-color:#<?=$oModuleInstanceColorState; ?>" class="CMSModuleChooserReadOnly"
         spotname="<?=$menuPrefix; ?>">
        <?php if (!is_null($data['oModule'])) {
    ?>
        <div id="moduleheaderline_<?=$menuPrefix; ?>" style="background-color: #81A6DD; height: 20px;">
            <div class="cleardiv">&nbsp;</div>
        </div>
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
        <div class="moduleInfo"><strong><?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.template_engine.module_view')); ?>
            :</strong> <?=TGlobal::OutHTML(str_replace('_', ' ', $sViewName)); ?></div>
        <div class="moduleInfo"><strong><?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.template_engine.slot_content')); ?>
            : </strong><?=TGlobal::OutHTML($data['oModuleInstance']->sqlData['name']); ?></div>
        <?php
} else {
        ?>
        <div style="background-image: url(/chameleon/blackbox/images/header_bg.gif);">
            <div class="moduleType"
                 style="background: url(<?=TGlobal::GetPathTheme(); ?>/images/icons/cross.png); background-repeat: no-repeat; background-position: 3px;"><?php echo TGlobal::Translate('chameleon_system_core.template_engine.slot_is_empty'); ?></div>
        </div>
        <?php
    } ?>
    </div>
</div>
