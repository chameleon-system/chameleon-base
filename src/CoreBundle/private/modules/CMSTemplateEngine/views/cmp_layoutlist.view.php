<?php

?>
<!-- show list of layouts -->
<form name="setpagedef" method="post" action="<?=PATH_CMS_CONTROLLER; ?>" target="_top" accept-charset="UTF-8">
    <input type="hidden" name="pagedef" value="templateengine"/>
    <input type="hidden" name="id" value="<?=$data['id']; ?>"/>
    <input type="hidden" name="sourcepagedef" value=""/>
    <input type="hidden" name="module_fnc[templateengine]" value="SetLayout"/>
</form>
<ul id="pageLayoutList">
    <?php
    while ($oPageLayout = $data['oMasterDefs']->Next()) {
        /** @var $oPageLayout TdbCmsMasterPagedef */
        $bIsActiveLayout = ($data['sActivePageDef'] == $oPageLayout->id); ?>
        <li class="<?php if (($data['sActivePageDef']) == $oPageLayout->id) {
            echo 'layoutitemactive';
        } ?>"
            onClick="parent.document.getElementById('userwebpageiframe').src='<?=URL_WEB_CONTROLLER; ?>?pagedef=<?=$data['id']; ?>&__masterPageDef=true&__modulechooser=true&id=<?=TGlobal::OutHTML(urlencode($oPageLayout->id)); ?>';">
            <div class="extraBold"><?=TGlobal::OutHTML($oPageLayout->sqlData['name']); ?></div>
            <div class="cleardiv">&nbsp;</div>
            <div class="pageTitle" style="float: left; width: 60px;"><?=TGlobal::Translate('chameleon_system_core.template_engine.spot_count'); ?></div>
            <div class="pageTitleValue" style="float:left;"><?=$oPageLayout->NumberOfDynamicModules(); ?></div>
            <div class="cleardiv">&nbsp;</div>
            <div class="pageTitleValue" style="padding-bottom: 5px;">
                <?=nl2br(TGlobal::OutHTML($oPageLayout->sqlData['description'])); ?>
            </div>
            <?php if (!$bIsActiveLayout) {
            ?>
            <div>
                <?=TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.template_engine.action_use_page_template'), "javascript:document.setpagedef.sourcepagedef.value='".TGlobal::OutHTML($oPageLayout->id)."';document.setpagedef.submit();", TGlobal::GetPathTheme().'/images/icons/accept.png'); ?>
            </div>
            <?php
        } ?>
        </li>
        <?php
    }
    ?>
</ul>
