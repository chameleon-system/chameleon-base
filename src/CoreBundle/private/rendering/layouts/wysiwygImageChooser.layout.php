<?php include dirname(__FILE__).'/includes/cms_head_data.inc.php'; ?>
<!--#CMSHEADERCODE#-->
<script src="<?=URL_CMS; ?>/javascript/wysiwygImage.js" type="text/javascript"></script>
</head>
<body>
<div class="dialog_content">
    <?php
    $modules->GetModule('content');
    ?>
</div>
 <?php
require dirname(__FILE__).'/includes/cms_footer_data.inc.php';
?>
</body>
</html>