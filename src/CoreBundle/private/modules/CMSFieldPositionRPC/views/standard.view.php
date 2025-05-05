<form name="poslistform" id="poslistform" method="post" action="<?php echo PATH_CMS_CONTROLLER; ?>" style="margin 0; padding 0;"
      accept-charset="UTF-8">
    <input type="hidden" name="pagedef" value="CMSFieldPositionRPC"/>
    <input type="hidden" name="module_fnc[contentmodule]" value="ExecuteAjaxCall"/>
    <input type="hidden" name="_fnc" value="SavePosChange"/>
    <input type="hidden" name="fieldName" value="<?php echo $data['fieldName']; ?>"/>
    <input type="hidden" name="tableSQLName" value="<?php echo $data['tableSQLName']; ?>"/>
    <input type="hidden" name="movedItemID" id="movedItemID" value=""/>
    <input type="hidden" name="activeItemId"  value="<?php echo TGlobal::OutHTML($recordID); ?>"/>

    <div class="pt-1">
    <?php
    if (!empty($data['list'])) {
        echo $data['list'];
    }
?>
    </div>
</form>