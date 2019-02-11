<?php require_once PATH_LAYOUTTEMPLATES.'/includes/cms_head_data.inc.php'; ?>
<header class="app-header navbar">
<?php $modules->GetModule('headerimage'); ?>
</header>
<div id="cmscontainer" class="app-body">
    <?php $modules->GetModule('sidebar') ?>
    <main class="main" id="cmscontentcontainer">
        <?php $modules->GetModule('breadcrumb'); ?>
        <div class="container-fluid">
        <?php $modules->GetModule('templateengine'); ?>
        </div>
    </main>
</div>
<?php require_once PATH_LAYOUTTEMPLATES.'/includes/footer.inc.php'; ?>
</body>
</html>