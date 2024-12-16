<!--isn't used anywhere (no pagedef has this layout) -->

<?php
$bodyAttributes = ' bgcolor="threedface"';
require_once dirname(__FILE__).'/includes/cms_head_data_light.inc.php'; ?>
<div parseWidgets="false">
    <?php
    $modules->GetModule('contentmodule');
    ?>
</div>
</body>
</html>