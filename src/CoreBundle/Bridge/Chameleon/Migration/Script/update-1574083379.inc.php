<h1>Build #1574083379</h1>
<h2>Date: 2019-11-18</h2>
<div class="changelog">
    - #107: add module: "Backend Navigation Tree"
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_tpl_module', 'en')
  ->setFields([
      'name' => 'Backend Navigation Tree',
      'description' => '',
      'classname' => 'chameleon_system_core.module.navigation_tree',
      'icon_list' => 'application.png',
      'icon_font_css_class' => '',
      'view_mapper_config' => 'standard=NavigationTree/standard.html.twig',
      'mapper_chain' => '',
      'view_mapping' => '',
      'revision_management_active' => '0',
      'is_copy_allowed' => '0',
      'show_in_template_engine' => '0',
      'is_restricted' => '0',
      'position' => '',
      'id' => 'c9fee34c-76c5-ef8a-c639-b18c821229e8',
  ])
;
TCMSLogChange::insert(__LINE__, $data);


