<div class="ModuleSearchForm">
    <form name="" id="" method="get" action="<?=TGlobal::OutHTML($data['searchURL']); ?>" accept-charset="UTF-8">
        <h3><?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.module_search.search_box_headline')); ?></h3>
        <input type="text" name="q" value="<?=TGlobal::OutHTML($data['q']); ?>" />
        <input type="submit" value="<?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.module_search.action_run_search')); ?>" />
    </form>
</div>