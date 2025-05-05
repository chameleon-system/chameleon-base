<form id="cmsform" name="cmsform" method="get" action="<?php echo PATH_CMS_CONTROLLER; ?>" target="_top" accept-charset="UTF-8">
    <input type="hidden" name="tableid" value="<?php echo TGlobal::OutHTML($data['id']); ?>"/>
    <input type="hidden" name="pagedef" value="tableeditor"/>
    <input type="hidden" name="id" value=""/>
    <input type="hidden" name="module_fnc[contentmodule]" value=""/>
    <?php foreach ($data['aHiddenFields'] as $key => $value) {
        ?>
    <input type="hidden" name="<?php echo TGlobal::OutHTML($key); ?>" value="<?php echo TGlobal::OutHTML($value); ?>"/>
    <?php
    } ?>
</form>
<form id="cmsformdel" name="cmsformdel" method="post" action="<?php echo PATH_CMS_CONTROLLER; ?>" target="_top" accept-charset="UTF-8">
    <input type="hidden" name="tableid" value="<?php echo TGlobal::OutHTML($data['id']); ?>"/>
    <input type="hidden" name="pagedef" value="tableeditor"/>
    <input type="hidden" name="id" value=""/>
    <input type="hidden" name="module_fnc[contentmodule]" value=""/>
    <?php foreach ($data['aHiddenFields'] as $key => $value) {
        ?>
    <input type="hidden" name="<?php echo TGlobal::OutHTML($key); ?>" value="<?php echo TGlobal::OutHTML($value); ?>"/>
    <?php
    } ?>
</form>
<form id="cmsformworkonlist" name="cmsformworkonlist" method="post" action="<?php echo PATH_CMS_CONTROLLER; ?>" accept-charset="UTF-8">
    <input type="hidden" name="pagedef" value="<?php echo $data['pagedef']; ?>"/>
    <input type="hidden" name="id" value="<?php echo TGlobal::OutHTML($data['id']); ?>"/>
    <input type="hidden" name="items" value=""/>
    <input type="hidden" name="module_fnc[contentmodule]" value=""/>
    <?php foreach ($data['aHiddenFields'] as $key => $value) {
        ?>
    <input type="hidden" name="<?php echo TGlobal::OutHTML($key); ?>" value="<?php echo TGlobal::OutHTML($value); ?>"/>
    <?php
    } ?>
</form>
<?php if ($data['permission_new']) {
    ?>
<div data-table-function-bar class="row button-element">
        <?php
    $data['oMenuItems']->GoToStart();
    /** @var $oMenuItem TCMSTableEditorMenuItem */
    while ($oMenuItem = $data['oMenuItems']->Next()) {
        echo '<div class="button-item col-12 col-sm-6 col-md-4 col-lg-auto">';
        echo $oMenuItem->GetMenuItemHTML();
        echo '</div>';
    } ?>
</div>
<?php
} ?>
<div class="card card-accent-primary">
    <div class="card-body p-0">
    <?php echo $data['sTable']; ?>
    </div>
</div>
