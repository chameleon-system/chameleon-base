<h1>Build #1520933354</h1>
<h2>Date: 2018-03-13</h2>
<div class="changelog">
    - Use service IDs for cronjobs.
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_cronjobs', 'en')
  ->setFields([
      'cron_class' => 'chameleon_system_url_alias.cronjob.delete_old_url_alias_entries_cronjob',
  ])
  ->setWhereEquals([
      'cron_class' => 'TCMSCronJob_DeleteOldCmsUrlAliasEntries',
  ])
;
TCMSLogChange::update(__LINE__, $data);
