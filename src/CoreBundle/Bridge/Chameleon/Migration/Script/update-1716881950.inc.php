<h1>Build #1716881950</h1>
<h2>Date: 2024-06-11</h2>
<div class="changelog">
    #29569: table cms_field_type: Set fieldclass to lenght = 255 to prevent the class name string from being truncated.
</div>
<?php


$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields([
//      'name' => 'fieldclass',
//      'translation' => 'PHP Klasse',
        'length_set' => '255',
    ])
    ->setWhereEquals([
        //gehört zur Tabelle "cms_field_type"
        'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_field_type'),
        'name' => 'fieldclass',
    ])
;
TCMSLogChange::update(__LINE__, $data);

$query ="ALTER TABLE `cms_field_type`
                     CHANGE `fieldclass`
                            `fieldclass` VARCHAR(255) NOT NULL COMMENT 'PHP Klasse: Die PHP-Feldklasse die für diesen Feldtypen verwendet werden soll'";
TCMSLogChange::RunQuery(__LINE__, $query);

