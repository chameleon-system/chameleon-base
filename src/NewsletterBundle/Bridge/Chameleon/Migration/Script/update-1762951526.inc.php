<h1>Build #1762951526</h1>
<h2>Date: 2025-11-12</h2>
<div class="changelog">
    - #68412: create media folder for newsletter imports
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_media_tree', 'de')
  ->setFields([
      'parent_id' => '1',
      'name' => 'Neuer Ordner',
      'entry_sort' => '27',
      'id' => '2bf85916-fc60-6f9c-985d-1ac597cbb767',
  ])
;
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_media_tree', 'de')
  ->setFields([
      'path_cache' => '/media/neuer-ordner', // prev.: ''
  ])
  ->setWhereEquals([
      'id' => '2bf85916-fc60-6f9c-985d-1ac597cbb767',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_media_tree', 'de')
  ->setFields([
      'name' => 'Newsletter Importe', // prev.: 'Neuer Ordner'
  ])
  ->setWhereEquals([
      'id' => '2bf85916-fc60-6f9c-985d-1ac597cbb767',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_media_tree', 'de')
  ->setFields([
      'path_cache' => '/media/newsletter-importe', // prev.: '/media/neuer-ordner'
  ])
  ->setWhereEquals([
      'id' => '2bf85916-fc60-6f9c-985d-1ac597cbb767',
  ])
;
TCMSLogChange::update(__LINE__, $data);

