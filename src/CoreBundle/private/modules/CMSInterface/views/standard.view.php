<div class="CMSInterfacePopup">
    <?php if (count($data['aMessages']) > 0) {
    ?>
    <div>
        <?php foreach ($data['aMessages'] as $sMessage) {
        ?>
        <div><?=$sMessage; ?></div>
        <?php
    } ?>
    </div>
    <?php
} ?>
    <form method="post" action="<?=PATH_CMS_CONTROLLER; ?>" accept-charset="UTF-8">
        <input type="hidden" name="pagedef" value="<?=TGlobal::OutHTML($data['pagedef']); ?>"/>
        <input type="hidden" name="module_fnc[<?=TGlobal::OutHTML($data['sModuleSpotName']); ?>]" value=""/>
        <?php
        while ($oInterface = $data['oInterfaces']->Next()) {
            echo '<div style="padding: 15px; padding-bottom: 0px;">
                    <div style="float:left;">
                      <input type="radio" value="'.$oInterface->id.'" id="'.$oInterface->id.'" name="iInterfaceId" />
                    </div>
                    <div>
                      <label for="'.$oInterface->id.'">
                        <strong>'.$oInterface->GetName().'</strong><br />
                        '.$oInterface->sqlData['description'].'
                      </label>
                    </div>
                    <div class="cleardiv">&nbsp;</div>
                  </div>';
        }
        ?>
        <input
            style="margin-top: 20px;"
            type="submit"
            value="<?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.cms_module_interface.action_run')); ?>"
            onClick="if (confirm('<?=TGlobal::OutHTML(TGlobal::Translate('chameleon_system_core.cms_module_interface.action_run_confirm')); ?>')) {
                return true
                } else {
                return false
                }"
            />
    </form>
</div>