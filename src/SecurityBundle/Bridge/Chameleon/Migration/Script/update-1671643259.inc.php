<h1>Build #1671643259</h1>
<h2>Date: 2022-12-21</h2>
<div class="changelog">
    - Access management.
</div>
<?php
TCMSLogChange::AddExtensionAutoParentToTable(
    'cms_menu_custom_item',
    '\ChameleonSystem\SecurityBundle\Bridge\Chameleon\TableObject\CmsMenuCustomItem'
);
TCMSLogChange::AddExtensionAutoParentToTable(
    'cms_module',
    '\ChameleonSystem\SecurityBundle\Bridge\Chameleon\TableObject\CmsModule'
);
