<h1>update - Build #1502813410</h1>
<h2>Date: 2017-08-15</h2>
<div class="changelog">
</div>
<?php

  $tableId = TCMSLogChange::GetTableId('cms_config');

  $data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
      ->setFields(array(
          'modifier' => 'hidden',
          '049_helptext' => 'Hostname des SMTP-Servers, über den anstatt des PHP-internen Mailers E-Mails verschickt werden

@deprecated since 6.2.0 - SMTP credentials are now handled via parameters.yml',
      ))
      ->setWhereEquals(array(
          'id' => TCMSLogChange::GetTableFieldId($tableId, 'smtp_server'),
      ))
  ;
  TCMSLogChange::update(__LINE__, $data);

  $data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
      ->setFields(array(
          '049_helptext' => 'Hostname of the SMTP server which is used instead of the internal php mailer

@deprecated since 6.2.0 - SMTP credentials are now handled via parameters.yml',
      ))
      ->setWhereEquals(array(
          'id' => TCMSLogChange::GetTableFieldId($tableId, 'smtp_server'),
      ))
  ;
  TCMSLogChange::update(__LINE__, $data);

  $data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
      ->setFields(array(
          'modifier' => 'hidden',
          '049_helptext' => '@deprecated since 6.2.0 - SMTP credentials are now handled via parameters.yml',
      ))
      ->setWhereEquals(array(
          'id' => TCMSLogChange::GetTableFieldId($tableId, 'smtp_user'),
      ))
  ;
  TCMSLogChange::update(__LINE__, $data);

  $data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
      ->setFields(array(
          '049_helptext' => '@deprecated since 6.2.0 - SMTP credentials are now handled via parameters.yml',
      ))
      ->setWhereEquals(array(
          'id' => TCMSLogChange::GetTableFieldId($tableId, 'smtp_user'),
      ))
  ;
  TCMSLogChange::update(__LINE__, $data);

  $data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
      ->setFields(array(
          'modifier' => 'hidden',
          '049_helptext' => '@deprecated since 6.2.0 - SMTP credentials are now handled via parameters.yml',
      ))
      ->setWhereEquals(array(
          'id' => TCMSLogChange::GetTableFieldId($tableId, 'smtp_password'),
      ))
  ;
  TCMSLogChange::update(__LINE__, $data);

  $data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
      ->setFields(array(
          '049_helptext' => '@deprecated since 6.2.0 - SMTP credentials are now handled via parameters.yml',
      ))
      ->setWhereEquals(array(
          'id' => TCMSLogChange::GetTableFieldId($tableId, 'smtp_password'),
      ))
  ;
  TCMSLogChange::update(__LINE__, $data);

  $data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
      ->setFields(array(
          'modifier' => 'hidden',
          '049_helptext' => 'Standard: 25

@deprecated since 6.2.0 - SMTP credentials are now handled via parameters.yml',
      ))
      ->setWhereEquals(array(
          'id' => TCMSLogChange::GetTableFieldId($tableId, 'smtp_port'),
      ))
  ;
  TCMSLogChange::update(__LINE__, $data);

  $data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
      ->setFields(array(
          '049_helptext' => 'Default: 25

@deprecated since 6.2.0 - SMTP credentials are now handled via parameters.yml',
      ))
      ->setWhereEquals(array(
          'id' => TCMSLogChange::GetTableFieldId($tableId, 'smtp_port'),
      ))
;
  TCMSLogChange::update(__LINE__, $data);
