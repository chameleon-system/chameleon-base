<?php
header('Content-Type: text/html; charset=UTF-8');
include dirname(__FILE__).'/includes/cms_page_header.inc.php';
?>
<link href="<?=TGlobal::GetPathTheme(); ?>/css/layout.css" rel="stylesheet" type="text/css"/>
<link href="<?=TGlobal::GetPathTheme(); ?>/css/global.css" rel="stylesheet" type="text/css"/>
<?php include dirname(__FILE__).'/includes/global-php-vars.inc.php'; ?>
<!--#CMSHEADERCODE#-->
<script src="<?=URL_CMS; ?>/javascript/cms.js" type="text/javascript"></script>
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