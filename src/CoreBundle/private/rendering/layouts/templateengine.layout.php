<?php require_once PATH_LAYOUTTEMPLATES.'/includes/cms_head_data.inc.php'; ?>
<header class="app-header">
    <?php $modules->GetModule('headerimage'); ?>
</header>
<div id="cmscontainer" class="app-body">
    <div class="sidebar">
        <?php $modules->GetModule('sidebar') ?>
    </div>
    <main class="main" id="cmscontentcontainer">
        <?php $modules->GetModule('breadcrumb'); ?>
        <?php $modules->GetModule('templateengine'); ?>
    </main>
</div>
<?php require_once PATH_LAYOUTTEMPLATES.'/includes/footer.inc.php'; ?>
</body>
</html>