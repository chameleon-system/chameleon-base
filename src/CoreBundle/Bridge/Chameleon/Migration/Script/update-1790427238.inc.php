<h1>Build #1785409738</h1>
<h2>Date: 2026-09-26</h2>
<div class="changelog">
    - ref #71112: extend portal to add some runtime caching
</div>
<?php
TCMSLogChange::AddExtensionAutoParentToTable(
        'cms_portal',
        \ChameleonSystem\CoreBundle\Bridge\Chameleon\TableObject\CmsPortalTableObject::class
);