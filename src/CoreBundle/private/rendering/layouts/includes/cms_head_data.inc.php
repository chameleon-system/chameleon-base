<?php
if (false === headers_sent()) {
    header('Content-Type: text/html; charset=UTF-8');
}
include dirname(__FILE__).'/cms_page_header.inc.php';
include dirname(__FILE__).'/global-php-vars.inc.php';

if (false === isset($cssClasses)) {
    $cssClasses = 'app header-fixed';
} else {
    $cssClasses .= 'app header-fixed';
}

if (false === isset($bodyAttributes)) {
    $bodyAttributes = '';
}

?>
<!--#CMSHEADERCODE#-->
<link href="<?=TGlobal::GetPathTheme(); ?>/css/layout.css" rel="stylesheet" type="text/css"/>
<link href="<?=TGlobal::GetStaticURLToWebLib('/bootstrap/css/bootstrap.min.css?v4.1.3')?>" media="screen" rel="stylesheet" type="text/css" />
<link href="<?=TGlobal::GetPathTheme(); ?>/coreui/css/coreui-standalone.min.css" media="screen" rel="stylesheet" type="text/css" />
<link href="<?=TGlobal::GetPathTheme(); ?>/css/global.css" rel="stylesheet" type="text/css"/>
</head>
<body class="<?=$cssClasses?>" <?=$bodyAttributes?>>