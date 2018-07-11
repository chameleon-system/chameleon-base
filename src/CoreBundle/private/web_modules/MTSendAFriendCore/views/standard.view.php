<form method="post" action="" name="SendAFriend" id="SendAFriend" accept-charset="UTF-8" class="uniForm">
    <fieldset class="inlineLabels" style="width: 550px;">
        <input type="hidden" name="module_fnc[<?=TGlobal::OutHTML($data['sModuleSpotName']); ?>]" value="SendEMail"/>
        <?php
        echo $data['HTMLformHiddenFields'];
        echo $data['HTMLform'];
        ?>
        <div class="ctrlHolder">
            <p><?=TGlobal::Translate('chameleon_system_core.module_send_a_friend.data_use_info'); ?></p>
        </div>
        <div class="buttonHolder">
            <button type="submit" class="submitButton"><?=TGlobal::Translate('chameleon_system_core.module_send_a_friend.action_send'); ?></button>
        </div>
    </fieldset>
</form>