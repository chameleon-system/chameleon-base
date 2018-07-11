<h1>Build #1520932820</h1>
<h2>Date: 2018-03-13</h2>
<div class="changelog">
    - Use service IDs for cronjobs.
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_cronjobs', 'en')
  ->setFields([
      'cron_class' => 'chameleon_system_extranet.cronjob.cleanup_extranet_login_history_cronjob',
  ])
  ->setWhereEquals([
      'cron_class' => 'TCMSCronJob_CleanupExtranetLoginHistory',
  ])
;
TCMSLogChange::update(__LINE__, $data);
