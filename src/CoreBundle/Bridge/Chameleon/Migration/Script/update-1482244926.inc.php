<h1>update - Build #1482244926</h1>
<h2>Date: 2016-12-20</h2>
<div class="changelog">
    - remove Viddler integration
</div>
<?php

  $databaseConnection = TCMSLogChange::getDatabaseConnection();

  $data = TCMSLogChange::createMigrationQueryData('cms_cronjobs', 'en')
      ->setWhereEquals(array(
          'cron_class' => 'TCMSCronJob_VideoSyncViddler',
      ))
  ;
  TCMSLogChange::delete(__LINE__, $data);

  $data = TCMSLogChange::createMigrationQueryData('cms_message_manager_backend_message', 'de')
    ->setWhereEquals(array(
        'name' => 'VIDDLER_LOGIN',
    ))
  ;
  TCMSLogChange::delete(__LINE__, $data);
