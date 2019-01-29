<?php
$oGlobal = TGlobal::instance();
$fieldName = $oGlobal->GetUserData('fieldName'); /* if called through a field, this is the name of that field */
?>
<form id="cmsformdel" name="cmsformdel" method="post" action="<?=PATH_CMS_CONTROLLER; ?>" accept-charset="UTF-8">
    <input type="hidden" name="tableid" value="<?=TGlobal::OutHTML($data['id']); ?>"/>
    <input type="hidden" name="pagedef" value="tableeditor"/>
    <input type="hidden" name="id" value=""/>
    <input type="hidden" name="module_fnc[contentmodule]" value=""/>
    <input type="hidden" name="_isiniframe" value="true"/>
    <?php foreach ($data['aHiddenFields'] as $key => $value) {
    ?>
    <input type="hidden" name="<?=TGlobal::OutHTML($key); ?>" value="<?=TGlobal::OutHTML($value); ?>"/>
    <?php
} ?>
</form>
<form id="cmsform" name="cmsform" method="post" action="<?=PATH_CMS_CONTROLLER; ?>" target="_top" accept-charset="UTF-8">
    <input type="hidden" name="tableid" value="<?=TGlobal::OutHTML($data['id']); ?>"/>
    <input type="hidden" name="pagedef" value="tableeditor"/>
    <input type="hidden" name="id" value=""/>
    <input type="hidden" name="module_fnc[contentmodule]" value=""/>
    <?php foreach ($data['aHiddenFields'] as $key => $value) {
        ?>
    <input type="hidden" name="<?=TGlobal::OutHTML($key); ?>" value="<?=TGlobal::OutHTML($value); ?>"/>
    <?php
    } ?>
</form>
<form id="cmsformAjaxCall" name="cmsformAjaxCall" method="post" accept-charset="UTF-8">
    <input type="hidden" name="tableid" value="<?=TGlobal::OutHTML($data['id']); ?>"/>
    <input type="hidden" name="pagedef" value="tableeditor"/>
    <input type="hidden" name="id" value=""/>
    <input type="hidden" name="_fnc" value="GetName"/>
    <?php if ($fieldName) {
        ?><input type="hidden" name="fieldName"
                                     value="<?=TGlobal::OutHTML($fieldName); ?>"/><?php
    } ?>
    <input type="hidden" name="_noModuleFunction" value="true"/>
    <input type="hidden" name="module_fnc[contentmodule]" value="ExecuteAjaxCall"/>
    <?php foreach ($data['aHiddenFields'] as $key => $value) {
        ?>
    <input type="hidden" name="<?=TGlobal::OutHTML($key); ?>" value="<?=TGlobal::OutHTML($value); ?>"/>
    <?php
    } ?>
</form>
<form id="cmsformworkonlist" name="cmsformworkonlist" method="post" action="<?=PATH_CMS_CONTROLLER; ?>" accept-charset="UTF-8">
    <input type="hidden" name="id" value="<?=TGlobal::OutHTML($data['id']); ?>"/>
    <input type="hidden" name="pagedef" value="<?=TGlobal::OutHTML($data['pagedef']); ?>"/>
    <input type="hidden" name="items" value=""/>
    <input type="hidden" name="module_fnc[contentmodule]" value=""/>
    <input type="hidden" name="_isiniframe" value="true"/>
    <?php foreach ($data['aHiddenFields'] as $key => $value) {
        ?>
    <input type="hidden" name="<?=TGlobal::OutHTML($key); ?>" value="<?=TGlobal::OutHTML($value); ?>"/>
    <?php
    } ?>
</form>
<?php if ($data['permission_new']) {
        ?>
        <div class="btn-group1 row button-element p-2">
                <?php
                $data['oMenuItems']->GoToStart();
        /** @var $oMenuItem TCMSTableEditorMenuItem */
        while ($oMenuItem = $data['oMenuItems']->Next()) {
            echo '<div class="button-item col-12 col-sm-6 col-md-auto">';
                echo $oMenuItem->GetMenuItemHTML();
            echo '</div>';
        } ?>
        </div>
<?php
    } ?>
<div class="iframeInnerSpacing">
    <?php echo $data['sTable']; ?>
</div>