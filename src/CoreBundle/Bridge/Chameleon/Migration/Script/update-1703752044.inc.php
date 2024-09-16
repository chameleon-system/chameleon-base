<h1>Build #1703752044</h1>
<h2>Date: 2023-12-28</h2>
<div class="changelog">
    - ref #59244: fix property fields. Some property fields had the owner set to a plain value select field,
    instead of a parent field. That will not work for doctrine entities.
</div>
<?php
$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields([
        'fieldtype_config' => 'connectedTableName=cms_portal_navigation',
    ])
    ->setWhereEquals([
        'id' => TCMSLogChange::GetTableFieldId(TCMSLogChange::GetTableId('cms_portal'), 'property_navigations'),
    ]);
TCMSLogChange::update(__LINE__, $data);
$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields([
        'fieldtype_config' => 'connectedTableName=cms_division',
    ])
    ->setWhereEquals([
        'id' => TCMSLogChange::GetTableFieldId(TCMSLogChange::GetTableId('cms_portal'), 'cms_portal_divisions'),
    ]);
TCMSLogChange::update(__LINE__, $data);

if (false === TCMSLogChange::FieldExists('cms_migration_file', 'cms_migration_counter_id')) {
    // the field exists - but the entry in the cms_field_conf is missing
    $data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
        ->setFields([
            'name' => 'cms_migration_counter_id',
            'translation' => 'Gehört zum Migration Counter',
            'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_PROPERTY_PARENT_ID'),
            'cms_tbl_field_tab' => '',
            'isrequired' => '0',
            'fieldclass' => '',
            'modifier' => 'none',
            'field_default_value' => '',
            'length_set' => '',
            'fieldtype_config' => '',
            'restrict_to_groups' => '0',
            'field_width' => '0',
            'position' => '2178',
            '049_helptext' => '',
            'row_hexcolor' => '',
            'is_translatable' => '0',
            'validation_regex' => '',
            'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_migration_file'),
            'fieldclass_subtype' => '',
            'class_type' => 'Core',
            'id' => 'f3d955e9-3c47-978b-b9a2-2ea9f95edce1',
        ]);
    TCMSLogChange::insert(__LINE__, $data);
    $data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
        ->setFields([
            'translation' => 'Belongs to migration counter',
        ])->setWhereEquals(['id' => 'f3d955e9-3c47-978b-b9a2-2ea9f95edce1',]);
    TCMSLogChange::update(__LINE__, $data);
}


$tableFieldsToTransform = [
    'module_customlist_config_sortfields' => ['module_customlist_config_id',],
    'cms_tpl_page_cms_master_pagedef_spot' => ['cms_tpl_page_id', 'cms_master_pagedef_spot_id',],
    'cms_master_pagedef_spot_parameter' => ['cms_master_pagedef_spot_id',],
    'cms_portal_navigation' => ['cms_portal_id',],
    'cms_tbl_list_class' => ['cms_tbl_conf_id',],
    'cms_export_profiles_fields' => ['cms_export_profiles_id',],
    'cms_division' => ['cms_portal_id',],
    'cms_tbl_conf_restrictions' => ['cms_tbl_conf_id',],
    'cms_master_pagedef_spot' => ['cms_master_pagedef_id'],
    'cms_tree_node' => ['cms_tree_id',],
];

foreach ($tableFieldsToTransform as $tableName => $fields) {
    foreach ($fields as $field) {
        $data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
            ->setFields([
                'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_PROPERTY_PARENT_ID'),
            ])
            ->setWhereEquals([
                'id' => TCMSLogChange::GetTableFieldId(TCMSLogChange::GetTableId($tableName), $field),
            ]);
        TCMSLogChange::update(__LINE__, $data);
    }
}

