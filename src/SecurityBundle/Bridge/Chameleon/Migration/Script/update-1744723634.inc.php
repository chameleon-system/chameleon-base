<h1>update - Build #1744723634</h1>
<h2>Date: 2025-04-15</h2>
<div class="changelog">
    - added 2fa authentication information (active + secret)
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
        'id' => 'b2933d91-6555-6587-afd9-3dff38b31f1f',
        'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_user'),
        'name' => 'google_authenticator_secret',
        'translation' => '2FA Authentikator Secret', // prev.: ''
        'position' => '3188', // prev.: '0'
        'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_STRING'),
    ])
;
TCMSLogChange::insert(__LINE__, $data);

$query ="ALTER TABLE `cms_user`
                        ADD `google_authenticator_secret` VARCHAR(255) NOT NULL COMMENT '2FA Authentikator Secret: '";
TCMSLogChange::RunQuery(__LINE__, $query);