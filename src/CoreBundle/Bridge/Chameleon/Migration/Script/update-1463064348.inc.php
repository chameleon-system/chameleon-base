<h1>update - Build #1463064348</h1>
<h2>Date: 2016-05-12</h2>
<div class="changelog">
    - remove class extensions
</div>
<?php

  $databaseConnection = TCMSLogChange::getDatabaseConnection();

  $query = 'SELECT `id`, `class_extensions` FROM `cms_portal`';
  $result = $databaseConnection->fetchAll($query);
  foreach ($result as $row) {
      if ('' !== trim($row['class_extensions'])) {
          TCMSLogChange::addInfoMessage("Field cms_portal.class_extensions was deleted. In the record with ID {$row['id']}, this value was set: {$row['class_extensions']}", TCMSLogChange::INFO_MESSAGE_LEVEL_TODO);
      }
  }

$fieldId = TCMSLogChange::GetTableFieldId(TCMSLogChange::GetTableId('cms_portal'), 'class_extensions');

  $query = 'ALTER TABLE `cms_portal`
                       DROP `class_extensions` ';
  TCMSLogChange::RunQuery(__LINE__, $query);

  $data = TCMSLogChange::createMigrationQueryData('cms_field_conf_cms_usergroup_mlt', 'de')
      ->setWhereEquals(array(
          'source_id' => $fieldId,
      ))
  ;
  TCMSLogChange::delete(__LINE__, $data);

  $data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
      ->setWhereEquals(array(
          'id' => $fieldId,
      ))
  ;
  TCMSLogChange::delete(__LINE__, $data);

  $query = 'SELECT `id`, `class_extensions` FROM `cms_config`';
  $result = $databaseConnection->fetchAll($query);
  foreach ($result as $row) {
      if ('' !== trim($row['class_extensions'])) {
          TCMSLogChange::addInfoMessage("Field cms_config.class_extensions was deleted. In the record with ID {$row['id']}, this value was set: {$row['class_extensions']}", TCMSLogChange::INFO_MESSAGE_LEVEL_TODO);
      }
  }

  $fieldId = TCMSLogChange::GetTableFieldId(TCMSLogChange::GetTableId('cms_config'), 'class_extensions');

  $query = 'ALTER TABLE `cms_config`
                       DROP `class_extensions` ';
  TCMSLogChange::RunQuery(__LINE__, $query);

  $data = TCMSLogChange::createMigrationQueryData('cms_field_conf_cms_usergroup_mlt', 'de')
      ->setWhereEquals(array(
          'source_id' => $fieldId,
      ))
  ;
  TCMSLogChange::delete(__LINE__, $data);

  $data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
      ->setWhereEquals(array(
          'id' => $fieldId,
      ))
  ;
  TCMSLogChange::delete(__LINE__, $data);
