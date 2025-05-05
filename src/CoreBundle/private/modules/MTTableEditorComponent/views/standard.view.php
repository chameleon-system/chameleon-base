<?php
$oCMSUser = $data['oCMSUser']; /* @var $oCMSUser TCMSUser */
?>
<div>
    <?php
    require_once dirname(__FILE__).'/../../MTTableEditor/views/includes/sharedJS.inc.php';
require dirname(__FILE__).'/../../MTTableEditor/views/includes/menuItems.inc.php';
?>
</div>
<div class="tableeditcontainer">
    <?php
require_once dirname(__FILE__).'/../../MTTableEditor/views/includes/fieldsForm.inc.php';
?>
</div>
<div style="position: relative; bottom: -10px;">
</div>