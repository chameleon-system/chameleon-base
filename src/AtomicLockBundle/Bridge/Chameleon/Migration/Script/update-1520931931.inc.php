<h1>Build #1520931931</h1>
<h2>Date: 2018-03-13</h2>
<div class="changelog">
    - Use service IDs for cronjobs.
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_cronjobs', 'en')
  ->setFields([
      'cron_class' => 'chameleon_system_atomic_lock.cronjob.clear_atomic_locks_cronjob',
  ])
  ->setWhereEquals([
      'cron_class' => 'TCMSCronjob_ClearAtomicLocks',
  ])
;
TCMSLogChange::update(__LINE__, $data);
