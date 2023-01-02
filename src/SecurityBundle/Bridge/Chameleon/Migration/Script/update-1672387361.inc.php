<h1>Build #1672387361</h1>
<h2>Date: 2022-12-30</h2>
<div class="changelog">
    - Access management.
</div>
<?php
TCMSLogChange::AddExtensionAutoParentToTable(
    'cms_field_conf',
    '\ChameleonSystem\SecurityBundle\Bridge\Chameleon\TableObject\CmsFieldConf'
);
