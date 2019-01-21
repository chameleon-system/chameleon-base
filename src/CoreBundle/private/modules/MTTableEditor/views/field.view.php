<?php
/**
 * @var $oCMSUser TCMSUser
 * @var $oField   TCMSField
 */
?>
<div style="background-color: #F2F8FC;">
    <div data-frame-function-bar class="btn-group">
        <?php
        $data['oMenuItems']->GoToStart();
        /** @var $oMenuItem TCMSTableEditorMenuItem */
        while ($buttonItem = $data['oMenuItems']->Next()) {
            echo $buttonItem->GetMenuItemHTML();
        }
        ?>
    </div>

    <form name="cmseditform" id="cmseditform" method="post" action="<?= PATH_CMS_CONTROLLER; ?>"
          style="margin 0; padding 0;" accept-charset="UTF-8" onsubmit="CHAMELEON.CORE.showProcessingModal();">
        <input type="hidden" name="pagedef" value="tableeditor"/>
        <input type="hidden" name="tableid" value="<?= TGlobal::OutHTML($data['tableid']); ?>"/>
        <input type="hidden" name="id" value="<?= TGlobal::OutHTML($data['id']); ?>"/>
        <input type="hidden" name="referer_id" value="<?= TGlobal::OutHTML($data['referer_id']); ?>"/>
        <input type="hidden" name="referer_table" value="<?= TGlobal::OutHTML($data['referer_table']); ?>"/>
        <input type="hidden" name="_fieldName" value="<?= TGlobal::OutHTML($data['_fieldName']); ?>"/>
        <input type="hidden" name="_fnc" value="AjaxSaveField"/>
        <input type="hidden" name="module_fnc[contentmodule]" value="ExecuteAjaxCall"/>
        <?php foreach ($data['aHiddenFields'] as $key => $value) {
            ?>
            <input type="hidden" name="<?= TGlobal::OutHTML($key); ?>" value="<?= TGlobal::OutHTML($value); ?>"/>
        <?php
        } ?>
        <?php
        echo $oField->GetContent();
        ?>
    </form>
</div>