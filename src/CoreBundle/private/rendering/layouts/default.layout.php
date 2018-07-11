<?php require_once PATH_LAYOUTTEMPLATES.'/includes/cms_head_data.inc.php'; ?>
<div id="cmscontainer">
    <?php $modules->GetModule('headerimage'); ?>
    <div id="cmscontentcontainer">
        <?php $modules->GetModule('contentmodule'); ?>
    </div><?php require_once PATH_LAYOUTTEMPLATES.'/includes/footer.inc.php'; ?>
</div>
</body>
</html>