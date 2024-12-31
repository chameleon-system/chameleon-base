<h1>Build #1735570737</h1>
<h2>Date: 2024-12-30</h2>
<div class="changelog">
    - ref #65351: reorder CMS template module field list ("name" ahead)
</div>
<?php

$cronjobTableId = TCMSLogChange::GetTableId('cms_tpl_module'); // Template modules

$fields = [
    // base tab fields
    'name', 'icon_list', 'description', 'icon_font_css_class', 'view_mapper_config', 'mapper_chain', 'view_mapping', 'revision_management_active', 'is_copy_allowed', 'show_in_template_engine', 'position', 'cms_tbl_conf_mlt', 'classname',
];

for ($i = 0; $i < count($fields) - 1; $i++) {
    TCMSLogChange::SetFieldPosition($cronjobTableId, $fields[$i + 1], $fields[$i]);
}
