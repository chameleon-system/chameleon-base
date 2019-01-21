<h1>Build #1543820426</h1>
<h2>Date: 2018-12-03</h2>
<div class="changelog">
    - #135: Add cronjobs enabled flag
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
      'name' => 'cronjobs_enabled',
      'translation' => 'Cronjobs aktiv',
      'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_BOOLEAN'),
      'cms_tbl_field_tab' => '',
      'isrequired' => '0',
      'fieldclass' => '',
      'modifier' => 'none',
      'field_default_value' => '1',
      'length_set' => '',
      'fieldtype_config' => '',
      'restrict_to_groups' => '0',
      'field_width' => '0',
      'position' => '2171',
      '049_helptext' => 'Gibt an, ob Cronjobs ausgeführt werden sollen. Falls die Cronjobs deaktiviert werden während sie gerade ausgeführt werden, wird der aktuelle Job regulär zu Ende ausgeführt, aber kein weiterer Job mehr gestartet.',
      'row_hexcolor' => '',
      'is_translatable' => '0',
      'validation_regex' => '',
      'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_config'),
      'fieldclass_subtype' => '',
      'class_type' => 'Core',
      'id' => 'f3d7a042-69ae-521f-4fd9-8c145d7571c3',
  ])
;
TCMSLogChange::insert(__LINE__, $data);

TCMSLogChange::SetFieldPosition(TCMSLogChange::GetTableId('cms_config'), 'cronjobs_enabled', 'shutdown_websites');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
        'translation' => 'Cronjobs enabled',
        '049_helptext' => 'Specifies if cron jobs should be executed. If cronjob execution is deactivated during execution the current job is still completed, but no further job gets started.',
    ])
    ->setWhereEquals([
        'id' => 'f3d7a042-69ae-521f-4fd9-8c145d7571c3',
    ])
;
TCMSLogChange::update(__LINE__, $data);

$query = "ALTER TABLE `cms_config`
                        ADD `cronjobs_enabled` ENUM('0','1') DEFAULT '1' NOT NULL COMMENT 'Cronjobs aktiv: Gibt an, ob Cronjobs ausgeführt werden sollen. Dies wird kurz vor der Ausführung eines jeden Cronjobs geprüft. Es kann also die Ausführung aller Cronjobs mittendrin unterbrochen werden.\\nDabei wird allerdings der gerade laufende nicht a'";
TCMSLogChange::RunQuery(__LINE__, $query);
