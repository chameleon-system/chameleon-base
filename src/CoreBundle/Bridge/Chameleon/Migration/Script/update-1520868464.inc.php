<h1>Build #1520868464</h1>
<h2>Date: 2018-03-12</h2>
<div class="changelog">
    - Use service IDs for cronjobs.
</div>
<?php

$fieldId = TCMSLogChange::GetTableFieldId(TCMSLogChange::GetTableId('cms_cronjobs'), 'cron_class');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
        'translation' => 'Class name/service ID',
        '049_helptext' => 'Either the ID of a service that was tagged with chameleon_system.cronjob and whose implementation extends TCMSCronJob. Or the name of a class which extends TCMSCronJob.',
    ])
    ->setWhereEquals([
        'id' => $fieldId,
    ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields([
        'translation' => 'Klassenname/Service ID',
        '049_helptext' => 'Entweder die ID eines Services, der mit chameleon_system.cronjob getaggt wurde und dessen Implementierung von TCMSCronJob ableitet. Oder der Name einer Klasse, die von TCMSCronJob ableitet.',
    ])
    ->setWhereEquals([
        'id' => $fieldId,
    ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_cronjobs', 'en')
    ->setWhereEquals([
        'cron_class' => 'TCMSCronJob_CleanInfoTasks',
    ])
;
TCMSLogChange::delete(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_cronjobs', 'en')
  ->setFields([
      'cron_class' => 'chameleon_system_core.cronjob.clear_complete_cache_cronjob',
  ])
  ->setWhereEquals([
      'cron_class' => 'TCMSCronJob_ClearCompleteCache',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_cronjobs', 'en')
  ->setFields([
      'cron_class' => 'chameleon_system_core.cronjob.clean_orphaned_mlt_connections_cronjob',
  ])
  ->setWhereEquals([
      'cron_class' => 'TCMSCronJob_CleanOrphanedMLTConnections',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_cronjobs', 'en')
  ->setFields([
      'cron_class' => 'chameleon_system_core.cronjob.clean_tags_cronjob',
  ])
  ->setWhereEquals([
      'cron_class' => 'TCMSCronJob_CleanTags',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_cronjobs', 'en')
  ->setFields([
      'cron_class' => 'chameleon_system_core.cronjob.cleanup_module_contents_cronjob',
  ])
  ->setWhereEquals([
      'cron_class' => 'TCMSCronJob_CleanupModuleContents',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_cronjobs', 'en')
  ->setFields([
      'cron_class' => 'chameleon_system_core.cronjob.clear_old_download_tokens_cronjob',
  ])
  ->setWhereEquals([
      'cron_class' => 'TCMSCronJob_ClearOldDownloadTokens',
  ])
;
TCMSLogChange::update(__LINE__, $data);
