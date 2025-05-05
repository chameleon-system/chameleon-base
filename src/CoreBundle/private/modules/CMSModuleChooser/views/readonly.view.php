<?php

/**
 * @var $oModule         \TdbCmsTplModule
 * @var $oModuleInstance \TdbCmsTplModuleInstance
 */
$menuPrefix = TGlobal::OutHTML($data['sModuleSpotName']);
?>
<div style="margin-top: 4px;position:relative;z-index:1000">
    <div style="border-color:#<?php echo $oModuleInstanceColorState; ?>" class="CMSModuleChooserReadOnly"
         spotname="<?php echo $menuPrefix; ?>">
        <?php
        if (null !== $oModule) {
            $iconFontCssClass = $oModule->fieldIconFontCssClass;
            if ('' === $iconFontCssClass) {
                $iconFontCssClass = 'fas fa-pen-square';
            } ?>
        <div id="moduleheaderline_<?php echo $menuPrefix; ?>" style="background-color: #81A6DD; height: 20px;">
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
                <i class="<?php echo TGlobal::OutHTML($iconFontCssClass); ?>"></i> <?php echo TGlobal::OutHTML($oModule->sqlData['name']); ?>
            </div>
        </div>
        <div class="moduleInfo"><strong><?php echo TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.template_engine.module_view')); ?>
            :</strong> <?php echo TGlobal::OutHTML(str_replace('_', ' ', $sViewName)); ?></div>
        <div class="moduleInfo"><strong><?php echo TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.template_engine.slot_content')); ?>
            : </strong><?php echo TGlobal::OutHTML($oModuleInstance->sqlData['name']); ?></div>
        <?php
        } else {
            ?>
        <div style="background-color: #63c2de">
            <div class="moduleType"><i class="fas fa-cube"></i> <?php echo ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.template_engine.slot_is_empty'); ?></div>
        </div>
        <?php
        } ?>
    </div>
</div>
