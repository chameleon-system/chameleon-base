<?php
header('Content-Type: text/html; charset=UTF-8');

?>
<?php include dirname(__FILE__).'/cms_page_header.inc.php'; ?>

<link href="<?=TGlobal::GetPathTheme(); ?>/css/layout.css" rel="stylesheet" type="text/css"/>

<script language="Javascript" type="text/javascript">
    /* global Variables coming from PHP */
    var _url_user_cms_public = '<?=URL_USER_CMS_PUBLIC; ?>';
    var _url_cms = '<?=URL_CMS; ?>';
    var _cmsurl = "<?=TGlobalBase::OutHTML(URL_CMS); ?>";
</script>
<!--#CMSHEADERCODE#-->
<script src="<?=URL_CMS; ?>/javascript/cms.js" type="text/javascript"></script>
<link href="<?=TGlobal::GetPathTheme(); ?>/css/global.css" rel="stylesheet" type="text/css"/>
</head>
  <body <?php
      if (isset($bodyAttributes)) {
          echo $bodyAttributes;
      }
      ?>>