<h1>Build #1534864767</h1>
<h2>Date: 2018-08-21</h2>
<div class="changelog">
    - change system name field for image crop templates: better help text, use unique text field
</div>
<?php

$query = "ALTER TABLE `cms_image_crop_preset` DROP INDEX `system_name`";
TCMSLogChange::RunQuery(__LINE__, $query);

$fieldId = TCMSLogChange::GetTableFieldId(TCMSLogChange::GetTableId('cms_image_crop_preset'), 'system_name');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields(
        [
            'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_STRING_UNIQUE'),
        ]
    )
    ->setWhereEquals(
        [
            'id' => $fieldId,
        ]
    );
TCMSLogChange::update(__LINE__, $data);

$query = "ALTER TABLE `cms_image_crop_preset` ADD UNIQUE ( `system_name` )";
TCMSLogChange::RunQuery(__LINE__, $query);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields(
        [
            '049_helptext' => 'The system name can be used in templates or other code to display a certain cutout. This is a purely technical value and should not be changed, as code might depend on it. For consistency it is recommended to assign only English names.',
        ]
    )
    ->setWhereEquals(
        [
            'id' => $fieldId,
        ]
    );
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields(
        [
            '049_helptext' => 'Der Systemname kann in Templates oder anderem Code verwendet werden, um einen bestimmten Ausschnitt anzuzeigen. Es handelt sich um einen rein technischen Wert, der nicht geändert werden sollte, da bestehender Code davon abhängen könnte. 
Für Einheitlichkeit im Code wird empfohlen, nur englischsprachige Systemnamen zu vergeben.',
        ]
    )
    ->setWhereEquals(
        [
            'id' => $fieldId,
        ]
    );
TCMSLogChange::update(__LINE__, $data);