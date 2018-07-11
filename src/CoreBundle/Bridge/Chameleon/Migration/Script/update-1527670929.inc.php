<h1>Build #1527670929</h1>
<h2>Date: 2018-05-30</h2>
<div class="changelog">
    - Restrict role management permissions to administrators.
</div>
<?php

TCMSLogChange::SetTableRolePermissions('cms_manager', 'cms_role', true);
TCMSLogChange::SetTableRolePermissions('cms_admin', 'cms_role', false, [0, 1, 2, 3]);
