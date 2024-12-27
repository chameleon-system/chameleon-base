<?php

use ChameleonSystem\CmsBackendBundle\BackendSession\BackendSessionInterface;
use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\UrlUtil;

/**
 * @var UrlUtil $urlUtil
 */
$urlUtil = ServiceLocator::get('chameleon_system_core.util.url');
/** @var BackendSessionInterface $backendSession */
$backendSession = ServiceLocator::get('chameleon_system_cms_backend.backend_session');
$previewLanguageId = $backendSession->getCurrentEditLanguageId();

?>
<form name="setpagedef" method="post" action="<?=PATH_CMS_CONTROLLER; ?>" target="_top" accept-charset="UTF-8">
    <input type="hidden" name="pagedef" value="templateengine"/>
    <input type="hidden" name="id" value="<?=$data['id']; ?>"/>
    <input type="hidden" name="sourcepagedef" value=""/>
    <input type="hidden" name="module_fnc[templateengine]" value="SetLayout"/>
</form>
<div class="p-2 mb-4">

    <div class="h4 mb-3"><?=TGlobal::Translate('chameleon_system_core.template_engine.headline_layout'); ?>:</div>
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

        <div class="card mb-3 <?php if ($bIsActiveLayout) {
            ?>text-white bg-success<?php
        } else {
            ?>bg-light<?php
        }?>">
            <div class="card-header p-2">
                <span class="card-title font-weight-bold mb-0"><?=TGlobal::OutHTML($oPageLayout->sqlData['name']); ?></span>
                <?php if (true === $bIsActiveLayout) {
            ?>
                <span class="badge badge-pill float-right"><?=TGlobal::Translate('chameleon_system_core.template_engine.active'); ?></span>
                <?php
        } ?>
            </div>
            <div class="card-body p-2">
                <div class="callout mt-0 mb-1 <?php
                if (false === $bIsActiveLayout) {
                    ?>callout-info<?php
                } ?>">
                    <span><?=TGlobal::Translate('chameleon_system_core.template_engine.spot_count'); ?></span>
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
                <div class="btn-group button-element">
                    <div class="button-item"><?=TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.template_engine.action_preview_template'), "javascript:console.log('".$url."');parent.document.getElementById('userwebpageiframe').src='".$url."';", 'far fa-eye'); ?></div>
                    <div class="button-item"><?=TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.template_engine.action_use_page_template'), "javascript:document.setpagedef.sourcepagedef.value='".TGlobal::OutHTML($oPageLayout->id)."';document.setpagedef.submit();", 'far fa-check-circle'); ?></div>
                </div>
            </div>
            <?php
            } else {
                ?>
            <div class="card-footer p-2 bg-success">
                <div class="btn-group button-element">
                    <div class="button-item"><?=TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.template_engine.action_preview_template'), "javascript:parent.document.getElementById('userwebpageiframe').src='".$url."';", 'far fa-eye'); ?></div>
                </div>
            </div>
            <?php
            } ?>
        </div>
        <?php
    }
    ?>
</div>
