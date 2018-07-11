<h1>update - Build #1505294288</h1>
<h2>Date: 2017-09-13</h2>
<div class="changelog">
    move cms_media fields into tabs
</div>
<?php

TCMSLogChange::requireBundleUpdates('ChameleonSystemCoreBundle', 1528119105);

$cmsMediaTableId = TCMSLogChange::GetTableId('cms_media');
$propertiesTabId = TCMSLogChange::createUnusedRecordId('cms_tbl_field_tab');
$data = TCMSLogChange::createMigrationQueryData('cms_tbl_field_tab', 'en')
    ->setFields(
        array(
            'systemname' => 'properties',
            'description' => '',
            'cms_tbl_conf_id' => $cmsMediaTableId,
            'name' => 'Properties / advanced',
            'position' => '1',
            'id' => $propertiesTabId,
        )
    );
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_field_tab', 'de')
    ->setFields(
        array(
            'name' => 'Eigenschaften / erweitert',
        )
    )
    ->setWhereEquals(
        array(
            'id' => $propertiesTabId,
        )
    );
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields(
        array(
            'cms_tbl_field_tab' => $propertiesTabId,
        )
    )
    ->setWhereEquals(
        array(
            'name' => 'filesize',
            'cms_tbl_conf_id' => $cmsMediaTableId,
        )
    );
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields(
        array(
            'cms_tbl_field_tab' => $propertiesTabId,
        )
    )
    ->setWhereEquals(
        array(
            'name' => 'cms_filetype_id',
            'cms_tbl_conf_id' => $cmsMediaTableId,
        )
    );
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields(
        array(
            'cms_tbl_field_tab' => $propertiesTabId,
        )
    )
    ->setWhereEquals(
        array(
            'name' => 'custom_filename',
            'cms_tbl_conf_id' => $cmsMediaTableId,
        )
    );
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields(
        array(
            'cms_tbl_field_tab' => $propertiesTabId,
        )
    )
    ->setWhereEquals(
        array(
            'name' => 'path',
            'cms_tbl_conf_id' => $cmsMediaTableId,
        )
    );
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields(
        array(
            'cms_tbl_field_tab' => $propertiesTabId,
        )
    )
    ->setWhereEquals(
        array(
            'name' => 'height',
            'cms_tbl_conf_id' => $cmsMediaTableId,
        )
    );
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields(
        array(
            'cms_tbl_field_tab' => $propertiesTabId,
        )
    )
    ->setWhereEquals(
        array(
            'name' => 'cms_media_tree_id',
            'cms_tbl_conf_id' => $cmsMediaTableId,
        )
    );
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields(
        array(
            'cms_tbl_field_tab' => $propertiesTabId,
        )
    )
    ->setWhereEquals(
        array(
            'name' => 'width',
            'cms_tbl_conf_id' => $cmsMediaTableId,
        )
    );
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields(
        array(
            'cms_tbl_field_tab' => $propertiesTabId,
        )
    )
    ->setWhereEquals(
        array(
            'name' => 'time_stamp',
            'cms_tbl_conf_id' => $cmsMediaTableId,
        )
    );
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields(
        array(
            'cms_tbl_field_tab' => $propertiesTabId,
        )
    )
    ->setWhereEquals(
        array(
            'name' => 'refresh_token',
            'cms_tbl_conf_id' => $cmsMediaTableId,
        )
    );
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields(
        array(
            'cms_tbl_field_tab' => $propertiesTabId,
        )
    )
    ->setWhereEquals(
        array(
            'name' => 'cms_user_id',
            'cms_tbl_conf_id' => $cmsMediaTableId,
        )
    );
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields(
        array(
            'modifier' => 'readonly',
        )
    )
    ->setWhereEquals(
        array(
            'name' => 'date_changed',
            'cms_tbl_conf_id' => $cmsMediaTableId,
        )
    );
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields(
        array(
            'cms_tbl_field_tab' => $propertiesTabId,
        )
    )
    ->setWhereEquals(
        array(
            'name' => 'cms_media_id',
            'cms_tbl_conf_id' => $cmsMediaTableId,
        )
    );
TCMSLogChange::update(__LINE__, $data);
