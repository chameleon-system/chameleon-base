<h1>update - Build #1516714288</h1>
<h2>Date: 2018-01-23</h2>
<div class="changelog">
    - Cleanup cms_config_parameter table
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_config_parameter', 'en')
    ->setWhereEquals(array(
        'systemname' => 'ARTICLE-IMAGE-CATEGORY-ID',
    ))
;
TCMSLogChange::delete(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_config_parameter', 'en')
  ->setWhereEquals(array(
      'systemname' => 'dbversion-counter',
  ))
;
TCMSLogChange::delete(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_config_parameter', 'en')
  ->setWhereEquals(array(
      'systemname' => 'dbversion-timestamp',
  ))
;
TCMSLogChange::delete(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_config_parameter', 'en')
    ->setWhereEquals(array(
        'systemname' => 'updateManagerVersion',
    ))
;
TCMSLogChange::delete(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_config_parameter', 'en')
    ->setWhereEquals(array(
        'systemname' => 'updateMetaCounters',
    ))
;
TCMSLogChange::delete(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_config_parameter', 'en')
    ->setWhereEquals(array(
        'systemname' => 'UseExtendedSessionValidation',
    ))
;
TCMSLogChange::delete(__LINE__, $data);
