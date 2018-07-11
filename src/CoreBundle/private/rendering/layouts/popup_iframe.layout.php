<?php
$bodyAttributes = ' style="background-color: #fff;"';
require_once dirname(__FILE__).'/includes/cms_head_data.inc.php';
?>
<div style="margin-right: 2px; margin-bottom: 2px;">
    <?php $modules->GetModule('contentmodule'); ?>
</div>
<?php
require_once dirname(__FILE__).'/includes/cms_footer_data.inc.php';
?>
</body>
</html>