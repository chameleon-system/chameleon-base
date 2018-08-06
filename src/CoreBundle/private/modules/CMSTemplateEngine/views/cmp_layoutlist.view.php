<?php

use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\UrlUtil;

/**
 * @var UrlUtil $urlUtil
 */
$urlUtil = ServiceLocator::get('chameleon_system_core.util.url');
$editLanguage = ServiceLocator::get('chameleon_system_core.language_service')->getActiveEditLanguage();
if (null === $editLanguage) {
    $previewLanguageId = TCMSConfig::GetInstance()->fieldTranslationBaseLanguageId;
} else {
    $previewLanguageId = $editLanguage->id;
}

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
        $bIsActiveLayout = ($data['sActivePageDef'] == $oPageLayout->id);
        $layoutItemClass = true === $bIsActiveLayout ? 'layoutitemactive' : '';

        $urlParameters = [
            'pagedef' => $data['id'],
            '__masterPageDef' => 'true',
            '__modulechooser' => 'true',
            'id' => TGlobal::OutHTML($oPageLayout->id),
            'previewLanguageId' => $previewLanguageId,
        ];
        $url = $urlUtil->getArrayAsUrl($urlParameters, URL_WEB_CONTROLLER.'?', '&');

        ?>
        <li class="<?= $layoutItemClass ?>" onClick="parent.document.getElementById('userwebpageiframe').src='<?= $url; ?>';">
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
