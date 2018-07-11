<h1>Build #1520930458</h1>
<h2>Date: 2018-03-13</h2>
<div class="changelog">
    - Use service IDs for cronjobs.
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_cronjobs', 'en')
  ->setFields([
      'cron_class' => 'chameleon_system_newsletter.cronjob.send_newsletter_cronjob',
  ])
  ->setWhereEquals([
      'cron_class' => 'TCMSCronJobSendNewsletter',
  ])
;
TCMSLogChange::update(__LINE__, $data);
