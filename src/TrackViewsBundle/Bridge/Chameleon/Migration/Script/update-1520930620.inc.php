<h1>Build #1520930620</h1>
<h2>Date: 2018-03-13</h2>
<div class="changelog">
    - Use service IDs for cronjobs.
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_cronjobs', 'en')
  ->setFields([
      'cron_class' => 'chameleon_system_track_views.cronjob.collect_views_cronjob',
  ])
  ->setWhereEquals([
      'cron_class' => 'TCMSCronJob_pkgTrackViewsCollectViews',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_cronjobs', 'en')
    ->setFields([
        'cron_class' => 'chameleon_system_track_views.cronjob.update_product_view_counter_cronjob',
    ])
    ->setWhereEquals([
        'cron_class' => 'TCMSCronJob_pkgTrackViewsUpdateShopArticleViewCounter',
    ])
;
TCMSLogChange::update(__LINE__, $data);
