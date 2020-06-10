<?php
/**
 * @var TModuleLoader $modules
 */
if (false === headers_sent()) {
    header('Content-Type: text/html; charset=UTF-8');
}
include __DIR__.'/cms_page_header.inc.php';
include __DIR__.'/global-php-vars.inc.php';

if (false === isset($cssClasses)) {
    $cssClasses = '';
}
$cssClasses .= ' app header-fixed';
if (true === $modules->hasModule('sidebar')) {
    $cssClasses .= ' sidebar-fixed sidebar-lg-show [{sidebarDisplayState}]';
}

if (false === isset($bodyAttributes)) {
    $bodyAttributes = '';
}

?>

<!--#CMSHEADERCODE#-->

<link href="<?=TGlobal::GetPathTheme(); ?>/css/layout.css" rel="stylesheet" type="text/css"/>
<link href="<?=TGlobal::GetStaticURLToWebLib('/bootstrap/css/bootstrap.min.css?v4.1.3'); ?>" media="screen" rel="stylesheet" type="text/css" />
<link href="<?=TGlobal::GetPathTheme(); ?>/coreui/css/coreui-standalone.min.css" media="screen" rel="stylesheet" type="text/css" />
<link href="<?=TGlobal::GetPathTheme(); ?>/css/global.css" rel="stylesheet" type="text/css"/>

<script src="<?= TGlobal::GetStaticURLToWebLib('/javascript/pnotify-3.2.0/pnotify.custom.min.js'); ?>" type="text/javascript"></script><!--#GLOBALRESOURCECOLLECTION#-->
<link href="<?= TGlobal::GetStaticURLToWebLib('/javascript/pnotify-3.2.0/pnotify.custom.min.css'); ?>" rel="stylesheet"/><!--#GLOBALRESOURCECOLLECTION#-->
<script src="<?= TGlobal::GetStaticURLToWebLib('/bootstrap/js/bootstrap.bundle.min.js?v4.1.3'); ?>" type="text/javascript"></script><!--#GLOBALRESOURCECOLLECTION#-->
<script src="<?= TGlobal::GetStaticURLToWebLib('/components/bootstrap3-typeahead/bootstrap3-typeahead.min.js'); ?>" type="text/javascript"></script><!--#GLOBALRESOURCECOLLECTION#-->
<script src="<?= TGlobal::GetStaticURLToWebLib('/javascript/jquery/jquery-form-4.2.2/jquery.form.min.js'); ?>" type="text/javascript"></script><!--#GLOBALRESOURCECOLLECTION#-->
<?php // Scrollbar JS needs to be loaded before CoreUI?>
<script src="<?= TGlobal::GetPathTheme(); ?>/coreui/js/perfect-scrollbar.min.js" type="text/javascript"></script><!--#GLOBALRESOURCECOLLECTION#-->
<script src="<?= TGlobal::GetPathTheme(); ?>/coreui/js/coreui.min.js" type="text/javascript"></script><!--#GLOBALRESOURCECOLLECTION#-->
<script src="<?= TGlobal::GetPathTheme(); ?>/coreui/js/coreui-utilities.min.js" type="text/javascript"></script><!--#GLOBALRESOURCECOLLECTION#-->
</head>
<body class="<?=$cssClasses; ?>" <?=$bodyAttributes; ?>>
