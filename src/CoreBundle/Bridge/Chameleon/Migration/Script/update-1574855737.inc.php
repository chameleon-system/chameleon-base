<h1>Build #1574855737</h1>
<h2>Date: 2019-11-27</h2>
<div class="changelog">
    - #107: add module: "Backend Navigation Tree Single Select Wysiwyg"
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_tpl_module', 'en')
  ->setFields([
      'name' => 'Backend Navigation Tree Single Select Wysiwyg',
      'description' => '',
      'classname' => 'chameleon_system_core.module.navigation_tree_single_select_wysiwyg',
      'icon_list' => 'application.png',
      'icon_font_css_class' => '',
      'view_mapper_config' => 'standard=NavigationTreeSingleSelectWysiwyg/standard.html.twig',
      'mapper_chain' => '',
      'view_mapping' => '',
      'revision_management_active' => '0',
      'is_copy_allowed' => '0',
      'show_in_template_engine' => '1',
      'is_restricted' => '0',
      'position' => '0',
      'id' => 'b526bd69-71ad-089d-ca71-d13dadc1a35d',
  ])
;
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tpl_module', 'de')
  ->setFields([
      'name' => 'Navigation Einzel-Auswahl Wysiwyg',
  ])
  ->setWhereEquals([
      'id' => 'b526bd69-71ad-089d-ca71-d13dadc1a35d',
  ])
;
TCMSLogChange::update(__LINE__, $data);

