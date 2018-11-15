<?php include dirname(__FILE__).'/includes/cms_page_header.inc.php'; ?>
<link href="<?=TGlobal::GetPathTheme(); ?>/css/layout.css" rel="stylesheet" type="text/css"/>
<link href="<?=TGlobal::GetPathTheme(); ?>/css/global.css" rel="stylesheet" type="text/css"/>
<link href="<?=TGlobal::GetPathTheme(); ?>/css/table.css" rel="stylesheet" type="text/css"/>
<script src="<?=URL_CMS; ?>/javascript/table.js" type="text/javascript"></script>
<?php  require_once dirname(__FILE__).'/includes/cms_head_data.inc.php'; ?>
</head>
<body style="background-color: #FFFFFF;">
<div>
    <?php $modules->GetModule('templateengine'); ?>
</div>
<?php
 require_once dirname(__FILE__).'/includes/cms_footer_data.inc.php';
?>
</body>
</html>
