<?php
require_once dirname(__FILE__).'/includes/sharedJS.inc.php';
require_once dirname(__FILE__).'/includes/rightClickMenu.inc.php';
require dirname(__FILE__).'/includes/menuItems.inc.php';
?>
</div>
<div class="card card-accent-primary mb-2">
    <div class="card-header p-1">
        <?php
        require_once dirname(__FILE__).'/includes/editorheader.inc.php';
        ?>
    </div>
    <div class="card-body p-0">
        <div class="tableeditcontainer">
            <?php
            require dirname(__FILE__).'/includes/fieldsForm.inc.php';
            ?>
        </div>
    </div>
</div>
<?php
require dirname(__FILE__).'/includes/menuItems.inc.php';
