<h1>core - Build #1471527481</h1>
<h2>Date: 2016-08-18</h2>
<div class="changelog">
    - remove workflow
</div>
<?php

  $data = TCMSLogChange::createMigrationQueryData('cms_cronjobs', 'de')
      ->setWhereEquals(array(
          'cron_class' => 'TCMSCronJob_CleanOldTransactions',
      ))
  ;
  TCMSLogChange::delete(__LINE__, $data);

  $data = TCMSLogChange::createMigrationQueryData('cms_cronjobs', 'de')
      ->setWhereEquals(array(
          'cron_class' => 'TCMSCronJob_PublishTransactionsInTime',
      ))
  ;
  TCMSLogChange::delete(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('data_mail_profile', 'de')
    ->setWhereEquals(array(
        'idcode' => 'workflowTransactionForward',
    ))
;
TCMSLogChange::delete(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('data_mail_profile', 'de')
    ->setWhereEquals(array(
        'idcode' => 'GetWorkflowTransactionOwnership',
    ))
;
TCMSLogChange::delete(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('data_mail_profile', 'de')
    ->setWhereEquals(array(
        'idcode' => 'workflowTransactionPublished',
    ))
;
TCMSLogChange::delete(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields(array(
        'modifier' => 'hidden',
    ))
    ->setWhereEquals(array(
        'name' => 'cms_role5_mlt',
    ))
;
TCMSLogChange::update(__LINE__, $data);
