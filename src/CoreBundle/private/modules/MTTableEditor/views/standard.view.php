<?php
require_once __DIR__.'/includes/sharedJS.inc.php';
require_once __DIR__.'/includes/rightClickMenu.inc.php';
require __DIR__.'/includes/menuItems.inc.php';
?>
<div class="card card-accent-primary mb-0" id="tableEditorContainer">
    <div class="card-header p-0">
        <?php
        require_once __DIR__.'/includes/editorheader.inc.php';
?>
    </div>
    <div class="card-body p-0">
        <div class="tableeditcontainer">
            <?php
    require __DIR__.'/includes/fieldsForm.inc.php';
?>
        </div>
    </div>
</div>
<?php
require __DIR__.'/includes/menuItems.inc.php';
