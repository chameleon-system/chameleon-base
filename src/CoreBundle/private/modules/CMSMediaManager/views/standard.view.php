<?php
require dirname(__FILE__).'/includes/javascripts.inc.php';
?>
<form method="post" action="<?=PATH_CMS_CONTROLLER; ?>" id="ajaxForm" name="ajaxForm"
      style="padding: 0px; padding: 0; margin: 0px; margin: 0;" accept-charset="UTF-8">
    <input type="hidden" name="pagedef" value="CMSMediaManager"/>
    <input type="hidden" name="module_fnc[content]" value="ExecuteAjaxCall"/>
    <input type="hidden" name="_fnc" id="_fnc" value=""/>
    <input type="hidden" name="fileID" id="fileID" value=""/>
</form>

<div style="float: left; width: 20%; padding-left: 10px; height: 95%; overflow: auto"">
    <h1>
        <?=TGlobal::Translate('chameleon_system_core.cms_module_media_manager.folder'); ?>
    </h1>
    <div id="treePlacer"></div>
</div>

<div style="float: left; width: 80%; height: 95%;">
        <div class="btn-group">
            <button type="button" class="btn btn-sm btn-info" onclick="cutSelectedFiles();"><span class="btn-icon" style="background-image: url(<?=TGlobal::GetPathTheme(); ?>/images/icons/cut.png);"><?php echo TGlobal::Translate('chameleon_system_core.action.cut'); ?></span></button>
            <button type="button" class="btn btn-sm btn-danger" onclick="deleteSelectedItem();"><span class="btn-icon" style="background-image: url(<?=TGlobal::GetPathTheme(); ?>/images/icons/delete.png);"><?php echo TGlobal::Translate('chameleon_system_core.action.delete'); ?></span></button>
        </div>
    <div style="height: 100%; background-color: #fff;">
        <iframe id="fileList" src="<?=$sListURL; ?>" border="0" frameborder="0" style="width:100%; height: 95%"></iframe>
    </div>
</div>
