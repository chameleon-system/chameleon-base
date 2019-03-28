<?php
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cronjobs</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link href="<?=TGlobal::GetPathTheme(); ?>/css/layout.css" rel="stylesheet" type="text/css"/>
    <link href="<?=TGlobal::GetStaticURLToWebLib('/bootstrap/css/bootstrap.min.css?v4.1.3'); ?>" media="screen" rel="stylesheet" type="text/css" />
    <link href="<?=TGlobal::GetPathTheme(); ?>/coreui/css/coreui-standalone.min.css" media="screen" rel="stylesheet" type="text/css" />
    <link href="<?=TGlobal::GetPathTheme(); ?>/css/global.css" rel="stylesheet" type="text/css"/>
</head>
<body style="background-color: transparent;">
<?php $modules->GetModule('main'); ?>
</body>
</html>