<?php require_once dirname(__FILE__).'/includes/cms_head_data.inc.php'; ?>
<div id="cmscontainer">
    <?php $modules->GetModule('headerimage'); ?>
    <div id="cmscontentcontainer">
        <?php $modules->GetModule('templateengine'); ?>
    </div>
    <?php require_once dirname(__FILE__).'/includes/footer.inc.php'; ?>
</div>
</body>
</html>
