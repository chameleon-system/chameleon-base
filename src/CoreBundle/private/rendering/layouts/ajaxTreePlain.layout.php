<?php
header('Content-Type: text/html; charset=UTF-8');
?>
<?php include dirname(__FILE__).'/includes/cms_page_header.inc.php'; ?>
<link href="<?=TGlobal::GetPathTheme(); ?>/css/layout.css" rel="stylesheet" type="text/css"/>
<link href="<?=TGlobal::GetPathTheme(); ?>/css/global.css" rel="stylesheet" type="text/css"/>
<?php include dirname(__FILE__).'/includes/global-php-vars.inc.php'; ?>
<!--#CMSHEADERCODE#-->
<script src="<?=URL_CMS; ?>/javascript/cms.js" type="text/javascript"></script>
</head>
<body style="background-color:#fff;">
<?php
$modules->GetModule('module');

require_once dirname(__FILE__).'/includes/cms_footer_data.inc.php';
?>
</body>
</html>
