<?php
if (TGlobal::CMSUserDefined()) {
    ?>
<script type="text/javascript">
    $(document).ready(function () {
        saveCMSRegistryEntry('dialogCloseButtonText', '<?=TGlobal::OutJS(TGlobal::Translate('chameleon_system_core.action.close')); ?>');
    });
</script>
<div id="pleaseWaitMessage">
    <div class="messageInnerDiv clearfix"><div class="pleaseWaitMessageContent"><?=nl2br(TGlobal::Translate('chameleon_system_core.text.wait')); ?></div></div>
</div>
<script src="<?=TGlobal::GetStaticURLToWebLib('/javascript/jquery/pnotify/pnotify.custom.min.js'); ?>" type="text/javascript"></script>
<link href="<?=TGlobal::GetStaticURLToWebLib('/javascript/jquery/pnotify/pnotify.custom.min.css'); ?>" rel="stylesheet" />
<?php
}

// message garbage collection
// dirty hack to prevent message shown on wrong table editor or table list instance
$oMessageManager = TCMSMessageManager::GetInstance();
$oMessageManager->ClearMessages();
