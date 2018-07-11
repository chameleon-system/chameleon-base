<?php
require_once dirname(__FILE__).'/includes/sharedJS.inc.php';
require_once dirname(__FILE__).'/includes/rightClickMenu.inc.php'; require dirname(__FILE__).'/includes/menuItems.inc.php';
?>
</div>
<div class="cmsBoxBorder" style="padding: 0;">
        <?php
        require_once dirname(__FILE__).'/includes/editorheader.inc.php';
        ?>
            <div class="tableeditcontainer">
                <div class="cleardiv" style="margin-bottom: 10px;">&nbsp;</div>
                 <?php
                require dirname(__FILE__).'/includes/fieldsForm.inc.php';
                ?>
            </div>
            <div style="position: relative; bottom: -25px;">
                 <?php
                require dirname(__FILE__).'/includes/menuItems.inc.php';
                ?>
            </div>
</div>