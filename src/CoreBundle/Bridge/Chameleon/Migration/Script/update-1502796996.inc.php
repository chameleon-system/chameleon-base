<h1>update - Build #1502796996</h1>
<h2>Date: 2017-08-15</h2>
<div class="changelog">
</div>
<?php

  $query = 'ALTER TABLE `cms_tpl_module` DROP INDEX `position`';
  TCMSLogChange::RunQuery(__LINE__, $query);

  $fieldId = TCMSLogChange::GetTableFieldId(TCMSLogChange::GetTableId('cms_tpl_module'), 'position');

  $data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
      ->setFields(array(
          'modifier' => 'hidden',
          '049_helptext' => '@deprecated since 6.2.0 - modules are now ordered alphabetically',
      ))
      ->setWhereEquals(array(
          'id' => $fieldId,
      ))
  ;
  TCMSLogChange::update(__LINE__, $data);

  $data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
      ->setFields(array(
          '049_helptext' => '@deprecated since 6.2.0 - modules are now ordered alphabetically',
      ))
      ->setWhereEquals(array(
          'id' => $fieldId,
      ))
  ;
  TCMSLogChange::update(__LINE__, $data);
