<form name="poslistform" id="poslistform" method="post" action="<?php echo PATH_CMS_CONTROLLER; ?>" style="margin 0; padding 0;"
      accept-charset="UTF-8">
    <input type="hidden" name="pagedef" value="CMSFieldMLTPosition"/>
    <input type="hidden" name="module_fnc[contentmodule]" value="ExecuteAjaxCall"/>
    <input type="hidden" name="_fnc" value="SavePosChange"/>
    <input type="hidden" name="tableSQLName" value="<?php echo $data['tableSQLName']; ?>"/>
    <input type="hidden" name="movedItemID" id="movedItemID" value=""/>
    <input type="hidden" name="sTargetTable" id="sTargetTable" value="<?php echo $data['sTargetTable']; ?>"/>
    <input type="hidden" name="sMltTableName" id="sMltTableName" value="<?php echo $data['sMltTableName']; ?>"/>
    <input type="hidden" name="sSourcerecordId" id="sSourcerecordId" value="<?php echo $data['sSourcerecordId']; ?>"/>
    <?php
    if (!empty($data['list'])) {
        echo $data['list'];
    }
?>
</form>