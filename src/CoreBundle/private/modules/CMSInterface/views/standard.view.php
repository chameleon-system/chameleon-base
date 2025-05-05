<div class="card">
    <div class="card-body">
    <?php if (count($data['aMessages']) > 0) {
        ?>
    <div>
        <?php foreach ($data['aMessages'] as $sMessage) {
            ?>
        <div class="alert alert-warning"><?php echo $sMessage; ?></div>
        <?php
        } ?>
    </div>
    <?php
    } ?>
    <form method="post" action="<?php echo PATH_CMS_CONTROLLER; ?>" accept-charset="UTF-8">
        <input type="hidden" name="pagedef" value="<?php echo TGlobal::OutHTML($data['pagedef']); ?>"/>
        <input type="hidden" name="module_fnc[<?php echo TGlobal::OutHTML($data['sModuleSpotName']); ?>]" value=""/>

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
            value="<?php echo TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_interface.action_run')); ?>"
            onClick="if (confirm('<?php echo TGlobal::OutHTML(ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_interface.action_run_confirm')); ?>')) {
                return true
                } else {
                return false
                }"
            />
        </div>
    </form>
    </div>
</div>