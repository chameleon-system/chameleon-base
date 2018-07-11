<?php
$bodyAttributes = 'class="backgroundcolor"';
require dirname(__FILE__).'/includes/cms_head_data.inc.php';

$modules->GetModule('content');

include dirname(__FILE__).'/includes/global-php-vars.inc.php';
$modules->GetModule('documentManager');
require_once dirname(__FILE__).'/includes/cms_footer_data.inc.php';
?>
</body>
</html>