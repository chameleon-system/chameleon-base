<?php require_once PATH_LAYOUTTEMPLATES.'/includes/cms_head_data.inc.php'; ?>
<body>
<?php $modules->GetModule('sidebar'); ?>

<div class="wrapper d-flex flex-column min-vh-100 bg-light dark:bg-transparent">
  <header class="header header-sticky mb-4">
      <?php $modules->GetModule('headerimage'); ?>
  </header>
  <main class="body main flex-grow-1 px-3">
    <div class="container-lg content" id="cmscontentcontainer">
        <?php $modules->GetModule('breadcrumb'); ?>
        <?php $modules->GetModule('contentmodule'); ?>
    </div>
  </main>
  <?php require_once PATH_LAYOUTTEMPLATES.'/includes/footer.inc.php'; ?>
</div>

</body>
</html>