<h1>Build #1520933797</h1>
<h2>Date: 2018-03-13</h2>
<div class="changelog">
    - Use service IDs for cronjobs.
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_cronjobs', 'en')
  ->setFields([
      'cron_class' => 'chameleon_system_cms_core_log.cronjob.cleanup_cronjob',
  ])
  ->setWhereEquals([
      'cron_class' => 'TPkgCmsCoreLogCleanupCronJob',
  ])
;
TCMSLogChange::update(__LINE__, $data);
