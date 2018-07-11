<form name="poslistform" id="poslistform" method="post" action="<?=PATH_CMS_CONTROLLER; ?>" style="margin 0; padding 0;"
      accept-charset="UTF-8">
    <input type="hidden" name="pagedef" value="CMSFieldMLTPosition"/>
    <input type="hidden" name="module_fnc[contentmodule]" value="ExecuteAjaxCall"/>
    <input type="hidden" name="_fnc" value="SavePosChange"/>
    <input type="hidden" name="tableSQLName" value="<?=$data['tableSQLName']; ?>"/>
    <input type="hidden" name="movedItemID" id="movedItemID" value=""/>
    <input type="hidden" name="sTargetTable" id="sTargetTable" value="<?=$data['sTargetTable']; ?>"/>
    <input type="hidden" name="sMltTableName" id="sMltTableName" value="<?=$data['sMltTableName']; ?>"/>
    <input type="hidden" name="sSourcerecordId" id="sSourcerecordId" value="<?=$data['sSourcerecordId']; ?>"/>
    <?php
    if (!empty($data['list'])) {
        echo $data['list'];
    }
    ?>
</form>