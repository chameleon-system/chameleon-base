<h1>Build #1780983145</h1>
<h2>Date: 2026-06-09</h2>
<div class="changelog">
    -
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
  ->setFields([
      'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_export_profiles'),
      'name' => 'new_field',
      'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_STRING'),
      'id' => '02ec6196-8562-aeae-ff10-4e3bd7fb5cc4',
  ])
;
TCMSLogChange::insert(__LINE__, $data);

$query ="ALTER TABLE `cms_export_profiles`
                        ADD `new_field` VARCHAR(255) NOT NULL";
TCMSLogChange::RunQuery(__LINE__, $query);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
      'name' => 'expand_multi_value_fields', // prev.: 'new_field'
      'translation' => 'Mehrwert-Felder separat darstellen', // prev.: ''
      'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_BOOLEAN'), // prev.: '34'
      'field_default_value' => '1', // prev.: ''
      'position' => '3289', // prev.: '0'
      '049_helptext' => 'Ist dieses Feld aktiviert, werden alle Felder mit einer 1:n Verbindung separat als Spalte im Export aufgeführt.

0: 
| Kunden-Nr |Kunde  |Kundengruppe  |
| --- | --- | --- |
| 1001 |Müller GmbH  |Händler, VIP, Rechnungskunde , Exportkunde |

1:

| Kunden-Nr | Kunde | Gruppe: Händler | Gruppe: VIP |Gruppe: Rechnungskunde  | Gruppe: Exportkunde |
| --- | --- | --- | --- | --- | --- |
| 1001 | Müller GmbH | 1 | 1 | 1 | 1 |', // prev.: ''
  ])
  ->setWhereEquals([
      'id' => '02ec6196-8562-aeae-ff10-4e3bd7fb5cc4',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
        ->setFields([
                'translation' => 'Export with multi value field view', // prev.: ''
                '049_helptext' => 'If active, all fields with a 1:n connection will be displayed with seperate columns in the export.

0: 
| Customer-Nr |Customer  |Customer Group  |
| --- | --- | --- |
| 1001 |Müller GmbH  |Händler, VIP, Rechnungskunde , Exportkunde |

1:

| Customer-Nr | Customer | Group: Händler | Group: VIP |Group: Rechnungskunde  | Group: Exportkunde |
| --- | --- | --- | --- | --- | --- |
| 1001 | Müller GmbH | 1 | 1 | 1 | 1 |', // prev.: ''
        ])
        ->setWhereEquals([
                'id' => '02ec6196-8562-aeae-ff10-4e3bd7fb5cc4',
        ])
;
TCMSLogChange::update(__LINE__, $data);

$query ="ALTER TABLE `cms_export_profiles`
                     CHANGE `new_field`
                            `expand_multi_value_fields` ENUM('0','1') DEFAULT '1' NOT NULL COMMENT 'Mehrwert-Felder separat darstellen: Ist dieses Feld aktiviert, werden alle Felder mit einer 1:n Verbindung separat als Spalte im Export aufgeführt.\\r\\n\\r\\n0: \\r\\n| Kunden-Nr |Kunde  |Kundengruppe  |\\r\\n| --- | --- | --- |\\r\\n| 1001 |Müller GmbH  |Händler, VIP, R'";
TCMSLogChange::RunQuery(__LINE__, $query);

$query ="ALTER TABLE `cms_export_profiles` ADD INDEX `expand_multi_value_fields` (`expand_multi_value_fields`)";
TCMSLogChange::RunQuery(__LINE__, $query);

