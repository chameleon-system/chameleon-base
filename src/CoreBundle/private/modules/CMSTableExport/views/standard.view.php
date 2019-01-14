<form method="get" action="<?=PATH_CMS_CONTROLLER; ?>" name="exportForm" id="exportForm">
    <input type="hidden" name="pagedef" value="CMSTableExport"/>
    <input type="hidden" name="_pagedefType" value="Core"/>
    <input type="hidden" name="listName" value="<?=$data['listName']; ?>"/>
    <input type="hidden" name="module_fnc[contentmodule]" value="GenerateExport"/>
    <input type="hidden" name="tableID" value="<?=$data['tableID']; ?>"/>
    <input type="hidden" name="listClass" value="<?=$data['listClass']; ?>"/>
    <input type="hidden" name="listCacheKey" value="<?=$data['listCacheKey']; ?>"/>

    <div style="float: left; line-height: 20px;"><?=TGlobal::Translate('chameleon_system_core.cms_module_table_export.form_select_profile'); ?>:
        &nbsp;</div>
    <div style="float: left;">
        <select name="cms_export_profile_id" class="form-control form-control-sm">
            <?php
            echo $data['profileOptions'];
            ?>
        </select>
    </div>
    <div style="float: left; padding-left: 10px;">
        <input type="button" class="btn btn-primary btn-sm" value="<?=TGlobal::Translate('chameleon_system_core.cms_module_table_export.action_generate'); ?>" onclick="forceFileDownloadForm('exportForm');" />
    </div>
    <div class="clearfix"></div>
</form>
<script>
    function forceFileDownloadForm(formName) {
        $.fileDownload($('#'+formName).prop('action'), {
            prepareCallback: function (url) {
                parent.CHAMELEON.CORE.showProcessingModal();
            },

            successCallback: function (url) {
                parent.jQuery.unblockUI();
            },
            httpMethod: "GET",
            data: $('#'+formName).serialize()
        });
    }
</script>