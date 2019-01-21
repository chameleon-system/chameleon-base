<?php

use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\UrlUtil;

/**
 * @var UrlUtil $urlUtil
 */
$urlUtil = ServiceLocator::get('chameleon_system_core.util.url');
/**
 * @var LanguageServiceInterface $languageService
 */
$languageService = ServiceLocator::get('chameleon_system_core.language_service');
$editLanguage = $languageService->getActiveEditLanguage();
if (null === $editLanguage) {
    $previewLanguageId = $languageService->getCmsBaseLanguageId();
} else {
    $previewLanguageId = $editLanguage->id;
}

?>
<form name="setpagedef" method="post" action="<?=PATH_CMS_CONTROLLER; ?>" target="_top" accept-charset="UTF-8">
    <input type="hidden" name="pagedef" value="templateengine"/>
    <input type="hidden" name="id" value="<?=$data['id']; ?>"/>
    <input type="hidden" name="sourcepagedef" value=""/>
    <input type="hidden" name="module_fnc[templateengine]" value="SetLayout"/>
</form>
<div class="p-2 mb-4">

    <h1 class="display-4"><?=TGlobal::Translate('chameleon_system_core.template_engine.headline_layout'); ?></h1>
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
        $url = $urlUtil->getArrayAsUrl($urlParameters, URL_WEB_CONTROLLER.'?', '&'); ?>
        <div class="card <?php if ($bIsActiveLayout) {
            ?>text-white bg-success<?php
        } ?>">
            <div class="card-header p-2">
                <span class="card-title mb-0"><?=TGlobal::OutHTML($oPageLayout->sqlData['name']); ?></span>
                <?php if (true === $bIsActiveLayout) {
            ?>
                <span class="badge badge-pill badge-light float-right"><?=TGlobal::Translate('chameleon_system_core.template_engine.active'); ?></span>
                <?php
        } ?>
            </div>
            <div class="card-body p-2">
                <div class="callout mt-0 mb-1 <?php
                if (false === $bIsActiveLayout) {
                    ?>callout-success<?php
                } ?>">
                    <small class="text-muted"><?=TGlobal::Translate('chameleon_system_core.template_engine.spot_count'); ?></small><br>
                    <strong class="h6"><?=$oPageLayout->NumberOfDynamicModules(); ?></strong>
                </div>
                <div class="card-text">
                    <small><?=nl2br(TGlobal::OutHTML($oPageLayout->sqlData['description'])); ?></small>
                </div>
            </div>
            <?php
            if (false === $bIsActiveLayout) {
                ?>
            <div class="card-footer p-2">
                <div class="btn-group">
                    <?=TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.template_engine.action_preview_template'), "javascript:parent.document.getElementById('userwebpageiframe').src=\''.$url.'\';", TGlobal::GetStaticURLToWebLib('/images/icons/eye.png')); ?>
                    <?=TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.template_engine.action_use_page_template'), "javascript:document.setpagedef.sourcepagedef.value='".TGlobal::OutHTML($oPageLayout->id)."';document.setpagedef.submit();", TGlobal::GetPathTheme().'/images/icons/accept.png'); ?>
                </div>
            </div>
            <?php
            } else {
                ?>
            <div class="card-footer p-2 bg-success">
                <?=TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.template_engine.action_preview_template'), "javascript:parent.document.getElementById('userwebpageiframe').src=\''.$url.'\';", TGlobal::GetStaticURLToWebLib('/images/icons/eye.png')); ?>
            </div>
            <?php
            } ?>
        </div>
        <?php
    }
    ?>
</div>