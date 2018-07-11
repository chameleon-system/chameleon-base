<h1>update - Build #1502701712</h1>
<h2>Date: 2017-08-14</h2>
<div class="changelog">
    - make module description and view translation translatable
</div>
<?php

$tableId = TCMSLogChange::GetTableId('cms_tpl_module');

$fieldNames = array(
        'description',
        'view_mapping',
);

foreach ($fieldNames as $fieldName) {
    TCMSLogChange::makeFieldMultilingual('cms_tpl_module', $fieldName);
}
