<?php
$cssClasses = 'backgroundcolor';
require dirname(__FILE__).'/includes/cms_head_data.inc.php';
echo "MediaManager-Layout\n";
$modules->GetModule('content');
$modules->GetModule('documentManager');
require_once dirname(__FILE__).'/includes/cms_footer_data.inc.php';
?>
</body>
</html>