<h1>core - Build #1476281556</h1>
<h2>Date: 2016-10-12</h2>
<div class="changelog">
    - remove dashboard widgets
</div>
<?php

TCMSLogChange::deleteTable('cms_widget_task');
TCMSLogChange::RunQuery(__LINE__, 'DROP TABLE IF EXISTS `cms_widget_task_cms_role`');

$data = TCMSLogChange::createMigrationQueryData('cms_content_box', 'en')
      ->setWhereEquals(array(
          'system_name' => 'system_tasks_and_messages',
      ))
  ;
  TCMSLogChange::delete(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_content_box', 'en')
    ->setFields(array(
        'class_path' => 'Core',
    ))
    ->setWhereEquals(array(
        'class_path' => 'DashboardWidgets/',
    ))
;
TCMSLogChange::update(__LINE__, $data);

$query = "ALTER TABLE `cms_content_box`
                     CHANGE `class_name`
                            `class_name` VARCHAR(255) NOT NULL COMMENT 'Widget class: When a class is entered here it will be searched for in the folder classes/DashboardWidgets/ while the widget classes name is attached as folder name.\\n\\n@deprecated since 6.2.0 - dashboard widgets are not supported anymore'";
TCMSLogChange::RunQuery(__LINE__, $query);

$cmsContentBoxTableId = TCMSLogChange::GetTableId('cms_content_box');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields(array(
        'modifier' => 'hidden',
        '049_helptext' => 'When a class is entered here it will be searched for in the folder classes/DashboardWidgets/ while the widget classes name is attached as folder name.

@deprecated since 6.2.0 - dashboard widgets are not supported anymore',
    ))
    ->setWhereEquals(array(
        'id' => TCMSLogChange::GetTableFieldId($cmsContentBoxTableId, 'class_name'),
    ))
;
TCMSLogChange::update(__LINE__, $data);

$query = "ALTER TABLE `cms_content_box`
                     CHANGE `class_type`
                            `class_type` ENUM('Core','Custom-Core','Customer') DEFAULT 'Core' NOT NULL COMMENT 'Widget class type: @deprecated since 6.2.0 - dashboard widgets are not supported anymore'";
TCMSLogChange::RunQuery(__LINE__, $query);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields(array(
        'modifier' => 'hidden',
        '049_helptext' => '@deprecated since 6.2.0 - dashboard widgets are not supported anymore',
    ))
    ->setWhereEquals(array(
        'id' => TCMSLogChange::GetTableFieldId($cmsContentBoxTableId, 'class_type'),
    ))
;
TCMSLogChange::update(__LINE__, $data);

$query = "ALTER TABLE `cms_content_box`
                     CHANGE `class_path`
                            `class_path` VARCHAR(255) DEFAULT 'DashboardWidgets/' NOT NULL COMMENT 'Widget class subfolder: Path after /library/classes/\\n\\n@deprecated since 6.2.0 - dashboard widgets are not supported anymore'";
TCMSLogChange::RunQuery(__LINE__, $query);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields(array(
        'modifier' => 'hidden',
        'field_default_value' => 'Core',
        '049_helptext' => 'Path after /library/classes/

@deprecated since 6.2.0 - dashboard widgets are not supported anymore',
    ))
    ->setWhereEquals(array(
        'id' => TCMSLogChange::GetTableFieldId($cmsContentBoxTableId, 'class_path'),
    ))
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields(array(
        '049_helptext' => 'Ist hier eine Klasse angegeben, wird diese im Ordner classes/DashboardWidgets/ gesucht, wobei der Klassenname des Widgets als Ordner angehängt wird.

@deprecated since 6.2.0 - dashboard widgets are not supported anymore',
    ))
    ->setWhereEquals(array(
        'id' => TCMSLogChange::GetTableFieldId($cmsContentBoxTableId, 'class_name'),
    ))
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields(array(
        '049_helptext' => '@deprecated since 6.2.0 - dashboard widgets are not supported anymore',
    ))
    ->setWhereEquals(array(
        'id' => TCMSLogChange::GetTableFieldId($cmsContentBoxTableId, 'class_type'),
    ))
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields(array(
        '049_helptext' => 'Pfad ab dem Ordner /library/classes/

@deprecated since 6.2.0 - dashboard widgets are not supported anymore',
    ))
    ->setWhereEquals(array(
        'id' => TCMSLogChange::GetTableFieldId($cmsContentBoxTableId, 'class_path'),
    ))
;
TCMSLogChange::update(__LINE__, $data);
