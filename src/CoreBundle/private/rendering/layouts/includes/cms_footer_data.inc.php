<?php
if (TGlobal::CMSUserDefined()) {
    ?>
    <script type="text/javascript">
        $(document).ready(function () {
            saveCMSRegistryEntry('dialogCloseButtonText', '<?=TGlobal::OutJS(TGlobal::Translate('chameleon_system_core.action.close')); ?>');
        });
    </script>

    <div class="modal" id="processingModal" tabindex="-1" role="dialog" aria-labelledby="processingDialog" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="alert alert-warning mb-0" role="alert">
                    <div class="lds-ring float-left mr-1">
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>
                    <div class="pt-2">
                        <strong><?= nl2br(TGlobal::Translate('chameleon_system_core.text.wait')); ?></strong></div>
                </div>
            </div>
        </div>
    </div>
    <script src="<?= TGlobal::GetStaticURLToWebLib('/javascript/pnotify-3.2.0/pnotify.custom.min.js'); ?>"
            type="text/javascript"></script>
    <link href="<?= TGlobal::GetStaticURLToWebLib('/javascript/pnotify-3.2.0/pnotify.custom.min.css'); ?>"
          rel="stylesheet"/>
    <script src="<?= TGlobal::GetStaticURLToWebLib('/bootstrap/js/bootstrap.bundle.min.js?v4.1.3'); ?>"
            type="text/javascript"></script>
    <script src="<?= TGlobal::GetStaticURLToWebLib('/components/bootstrap3-typeahead/bootstrap3-typeahead.min.js'); ?>" type="text/javascript"></script>
    <script src="<?= TGlobal::GetStaticURLToWebLib('/javascript/jquery/jquery-form-4.2.2/jquery.form.min.js'); ?>"
            type="text/javascript"></script>
    <script src="<?= TGlobal::GetStaticURLToWebLib('/javascript/cms.v1.js'); ?>" type="text/javascript"></script>
    <?php // Scrollbar JS needs to be loaded before CoreUI?>
    <script src="<?= TGlobal::GetPathTheme(); ?>/coreui/js/perfect-scrollbar.min.js" type="text/javascript"></script>
    <script src="<?= TGlobal::GetPathTheme(); ?>/coreui/js/coreui.min.js" type="text/javascript"></script>
    <script src="<?= TGlobal::GetPathTheme(); ?>/coreui/js/coreui-utilities.min.js" type="text/javascript"></script>
    <?php
}

// message garbage collection
// dirty hack to prevent message shown on wrong table editor or table list instance
$oMessageManager = TCMSMessageManager::GetInstance();
$oMessageManager->ClearMessages();
