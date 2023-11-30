<?php
/**
 * @var TModuleLoader $modules
 */
if (false === headers_sent()) {
    header('Content-Type: text/html; charset=UTF-8');
}
include __DIR__.'/cms_page_header.inc.php';
include __DIR__.'/global-php-vars.inc.php';


?>
<!--#CMSHEADERCODE#-->
<link href="<?=TGlobal::GetPathTheme(); ?>/css/layout.css" rel="stylesheet" type="text/css"/>
<link href="<?=TGlobal::GetStaticURLToWebLib('/bootstrap/css/bootstrap.min.css?v4.1.3'); ?>" media="screen" rel="stylesheet" type="text/css" />
<link href="<?=TGlobal::GetPathTheme(); ?>/coreui/coreuiTheme.css?v43" media="screen" rel="stylesheet" type="text/css" />
<link href="<?=TGlobal::GetPathTheme(); ?>/css/global.css" rel="stylesheet" type="text/css"/>
</head>