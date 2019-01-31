<?php
/**
 * @deprecated since 6.2.0 - no longer used.
 */
?>
<h1><?=TGlobal::Translate('chameleon_system_core.cms_module_cms_search.headline'); ?></h1>

<h2><?=TGlobal::Translate('chameleon_system_core.cms_module_cms_search.select_portal'); ?>:</h2>

<div style="padding: 10px 0px 10px 0px;">
    <form name="searchbuildform" id="searchbuildform" method="post" accept-charset="UTF-8">
        <input type="hidden" name="pagedef" value="CMSCreateSearchIndexPlain"/>
        <input type="hidden" name="module_fnc[<?=$data['sModuleSpotName']; ?>]" value="BuildIndex"/>
        <select name="cms_portal_id" id="cms_portal_id" class="form-control form-control-sm">
            <?=$data['portalOptions']; ?>
        </select>
    </form>
</div>

<?=TCMSRender::DrawButton(TGlobal::Translate('chameleon_system_core.cms_module_cms_search.action_start_index'), 'javascript:document.searchbuildform.submit();', 'far fa-play-circle'); ?>
<div class="cleardiv">&nbsp;</div>
<div id="searchStatus" style="display: none;">
    <h2 id="searchStatusLoader"><img
        src="<?=TGlobal::GetPathTheme(); ?>/images/loading.gif"/>&nbsp;&nbsp;<?=TGlobal::Translate('chameleon_system_core.cms_module_cms_search.state_index_running'); ?>
        ...</h2>
    <div><h1><?=TGlobal::Translate('chameleon_system_core.cms_module_cms_search.state_indexed'); ?>: <span id="indexcount">0</span></h1></div>
</div>