<?php
if (false === headers_sent()) {
    header('Content-Type: text/html; charset=UTF-8');
}
?>
<?php include dirname(__FILE__).'/cms_page_header.inc.php'; ?>
<link href="<?=TGlobal::GetPathTheme(); ?>/css/layout.css" rel="stylesheet" type="text/css"/>
<?php include dirname(__FILE__).'/global-php-vars.inc.php'; ?>
<!--#CMSHEADERCODE#-->
<link href="<?=TGlobal::GetPathTheme(); ?>/css/global.css" rel="stylesheet" type="text/css"/>
</head>
  <body class="app header-fixed" <?php
      if (isset($bodyAttributes)) {
          echo $bodyAttributes;
      }
      ?>>