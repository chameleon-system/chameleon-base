<h1>Build #1536589748</h1>
<h2>Date: 2018-09-10</h2>
<div class="changelog">
    - Configure minimum password length.
</div>
<?php


$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
  ->setFields([
      'fieldtype_config' => 'minimumLength=6',
  ])
  ->setWhereEquals([
      'id' => TCMSLogChange::GetTableFieldId(TCMSLogChange::GetTableId('data_extranet_user'), 'password'),
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_message_manager_message', 'en')
  ->setFields([
      'description' => 'Password too long.',
      'message' => 'Your password is too long. Please choose a shorter one.',
  ])
  ->setWhereEquals([
      'name' => 'ERROR-USER-REGISTER-PWD-TO-LONG',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_message_manager_message', 'de')
  ->setFields([
      'description' => 'Passwort ist zu lang.',
      'message' => 'Ihr Passwort ist zu lang. Bitte wählen Sie ein kürzeres Passwort.',
  ])
  ->setWhereEquals([
      'name' => 'ERROR-USER-REGISTER-PWD-TO-LONG',
  ])
;
TCMSLogChange::update(__LINE__, $data);

TCMSLogChange::addInfoMessage('Minimum password length for extranet users is set to 6 characters which was the fixed value before this release. Consider increasing the value to at least 8 (see field config of the extranet user password field). This might require updating user information for the password policy.', TCMSLogChange::INFO_MESSAGE_LEVEL_INFO);
