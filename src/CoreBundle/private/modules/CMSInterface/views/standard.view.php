<div>
    <?php if (count($data['aMessages']) > 0) {
    ?>
    <div>
        <?php foreach ($data['aMessages'] as $sMessage) {
        ?>
        <div class="alert alert-warning"><?=$sMessage; ?></div>
        <?php
    } ?>
    </div>
    <?php
} ?>
    <form method="post" action="<?=PATH_CMS_CONTROLLER; ?>" accept-charset="UTF-8">
        <input type="hidden" name="pagedef" value="<?=TGlobal::OutHTML($data['pagedef']); ?>"/>
        <input type="hidden" name="module_fnc[<?=TGlobal::OutHTML($data['sModuleSpotName']); ?>]" value=""/>

        <div class="list-group">
        <?php
        $checked = 'checked';
        while ($oInterface = $data['oInterfaces']->Next()) {
            echo '    <label for="'.$oInterface->id.'" class="list-group-item list-group-item-action">
                        <div class="d-flex">
                            <input type="radio" value="'.$oInterface->id.'" '.$checked.' id="'.$oInterface->id.'" name="iInterfaceId" class="mr-2" />
                            <div>
                                <h5>'.$oInterface->GetName().'</h5>
                                <p class="mb-1">'.$oInterface->sqlData['description'].'</p>
                            </div>
                        </div>
                      </label>
                      ';
            $checked = '';
        }
        ?>
        </div>
        <div class="mt-3">
        <input
            type="submit"
            class="btn btn-primary"
            value="<?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.cms_module_interface.action_run')); ?>"
            onClick="if (confirm('<?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.cms_module_interface.action_run_confirm')); ?>')) {
                return true
                } else {
                return false
                }"
            />
        </div>
    </form>
</div>
