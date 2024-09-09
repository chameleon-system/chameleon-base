<h1>update - Build #1691591541</h1>
<h2>Date: 2023-08-09</h2>
<div class="changelog">
    - ref #63039: add template module for BreadcrumbBundle
</div>
<?php
  $data = TCMSLogChange::createMigrationQueryData('cms_tpl_module', 'de')
      ->setFields([
          'name' => '',
          'description' => '',
          'icon_list' => 'application.png',
          'classname' => '',
          'view_mapper_config' => '',
          'mapper_chain' => '',
          'view_mapping' => '',
          'revision_management_active' => '0',
          'is_copy_allowed' => '0',
          'show_in_template_engine' => '1',
          'position' => '',
          'is_restricted' => '0',
          'id' => '5bdd674e-40a3-e347-a810-878d1b63750d',
      ]);
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tpl_module', 'de')
    ->setFields([
        'name' => 'Breadcrumb',
        'description' => 'Breadcrumb',
        'icon_list' => 'application.png',
        'classname' => 'chameleon_system_breadcrumb.module.breadcrumb',
        'view_mapper_config' => 'standard=standard.html.twig',
        'mapper_chain' => '',
        'view_mapping' => 'standard=Standard',
        'revision_management_active' => '0',
        'is_copy_allowed' => '0',
        'show_in_template_engine' => '1',
        'position' => '115',
        'is_restricted' => '0',
    ])
    ->setWhereEquals([
        'id' => '5bdd674e-40a3-e347-a810-878d1b63750d',
    ]);
TCMSLogChange::update(__LINE__, $data);
