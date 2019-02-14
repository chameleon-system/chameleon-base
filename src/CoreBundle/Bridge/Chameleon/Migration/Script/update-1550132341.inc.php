<h1>Build #1550132341</h1>
<h2>Date: 2019-02-14</h2>
<div class="changelog">
    - Add StaticView module
</div>
<?php

$moduleId = TCMSLogChange::createUnusedRecordId('cms_tpl_module');

$data = TCMSLogChange::createMigrationQueryData('cms_tpl_module', 'en')
  ->setFields([
      'description' => 'This module can be used to display static information in the backend.',
      'icon_list' => 'application.png',
      'view_mapper_config' => 'standard=StaticView/standard.html.twig',
      'mapper_chain' => '',
      'view_mapping' => '',
      'revision_management_active' => '0',
      'is_copy_allowed' => '0',
      'show_in_template_engine' => '0',
      'position' => '',
      'is_restricted' => '0',
      'name' => 'Static view',
      'classname' => 'chameleon_system_core.module.static_view_module',
      'id' => $moduleId,
  ])
;
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tpl_module', 'de')
    ->setFields([
        'description' => 'Mit diesem Modul können im Backend statische Informationen angezeigt werden.',
        'name' => 'Statische Ansicht',
    ])
    ->setWhereEquals([
        'id' => $moduleId,
    ])
;
TCMSLogChange::update(__LINE__, $data);
