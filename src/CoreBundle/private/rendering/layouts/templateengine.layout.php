<?php require_once PATH_LAYOUTTEMPLATES.'/includes/cms_head_data.inc.php'; ?>
<body>
<?php $modules->GetModule('sidebar'); ?>

<div class="wrapper d-flex flex-column min-vh-100 bg-light dark:bg-transparent">
  <header class="header header-sticky mb-4">
    <div class="container-fluid">
        <?php $modules->GetModule('headerimage'); ?>
    </div>
    <div class="header-divider"></div>
    <div class="container-fluid">
        <?php $modules->GetModule('breadcrumb'); ?>
    </div>
  </header>
  <main class="body main flex-grow-1 px-3">
    <div class="container-fluid content px-0" id="cmscontentcontainer">
        <?php $modules->GetModule('templateengine'); ?>
    </div>
  </main>
    <?php require_once PATH_LAYOUTTEMPLATES.'/includes/footer.inc.php'; ?>
</div>

</body>
</html>
