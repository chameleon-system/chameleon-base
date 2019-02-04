<h1>Build #1548168211</h1>
<h2>Date: 2019-01-22</h2>
<div class="changelog">
    - Add sidebar backend module.
</div>
<?php

$moduleId = TCMSLogChange::createUnusedRecordId('cms_tpl_module');

$data = TCMSLogChange::createMigrationQueryData('cms_tpl_module', 'en')
  ->setFields([
      'description' => 'This module provides the backend sidebar menu.',
      'icon_list' => 'application.png',
      'view_mapper_config' => 'standard=BackendSidebar/standard.html.twig',
      'mapper_chain' => '',
      'view_mapping' => '',
      'revision_management_active' => '0',
      'is_copy_allowed' => '0',
      'show_in_template_engine' => '0',
      'position' => '',
      'is_restricted' => '0',
      'name' => 'Sidebar backend module',
      'classname' => 'chameleon_system_core.module.sidebar.sidebar_backend_module',
      'id' => $moduleId,
  ])
;
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tpl_module', 'de')
  ->setFields([
      'description' => 'Dieses Modul stellt das Seitenmenü im Backend bereit.',
      'name' => 'Seitenmenü Backendmodul',
  ])
  ->setWhereEquals([
      'id' => $moduleId,
  ])
;
TCMSLogChange::update(__LINE__, $data);
