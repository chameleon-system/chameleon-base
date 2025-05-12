<h1>Build #1534346712</h1>
<h2>Date: 2018-08-15</h2>
<div class="changelog">
    - add German translations for cms_media.alt_tag and cms_media.systemname
</div>
<?php
$cmsMediaTableId = TCMSLogChange::GetTableId('cms_media');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields(
        [
            'translation' => 'Alt Tag',
        ]
    )
    ->setWhereEquals(
        [
            'cms_tbl_conf_id' => $cmsMediaTableId,
            'name' => 'alt_tag',
        ]
    );
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields(
        [
            'translation' => 'Systemname',
        ]
    )
    ->setWhereEquals(
        [
            'cms_tbl_conf_id' => $cmsMediaTableId,
            'name' => 'systemname',
        ]
    );
TCMSLogChange::update(__LINE__, $data);
