<h1>Build #1574693431</h1>
<h2>Date: 2019-11-25</h2>
<div class="changelog">
    - #107: add module: "Navigation Tree Single Select Backend Module" and correction of previous update: new pagedef "navigationTree"
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_tpl_module', 'en')
  ->setFields([
      'name' => 'Navigation Tree Single Select Backend Module',
      'description' => '',
      'classname' => 'chameleon_system_core.module.navigation_tree_single_select',
      'icon_list' => 'application.png',
      'icon_font_css_class' => '',
      'view_mapper_config' => 'standard=NavigationTreeSingleSelect/standard.html.twig',
      'mapper_chain' => '',
      'view_mapping' => '',
      'revision_management_active' => '0',
      'is_copy_allowed' => '0',
      'show_in_template_engine' => '1',
      'is_restricted' => '0',
      'position' => '',
      'id' => 'e01556f8-9ee5-9cd2-b451-8850da258002',
  ])
;
TCMSLogChange::insert(__LINE__, $data);


$data = TCMSLogChange::createMigrationQueryData('cms_tpl_module', 'de')
  ->setFields([
      'name' => 'Navigationsbaum Einzel-Auswahl Backendmodul',
  ])
  ->setWhereEquals([
      'id' => 'e01556f8-9ee5-9cd2-b451-8850da258002',
  ])
;
TCMSLogChange::update(__LINE__, $data);

// correction of pagedef, setting of previous update
$data = TCMSLogChange::createMigrationQueryData('cms_menu_custom_item', 'en')
    ->setFields([
        'url' => '/cms?pagedef=navigationTree&table=cms_tpl_page&noassign=1',
    ])
    ->setWhereEquals([
        'name' => 'Navigation',
    ])
;
TCMSLogChange::update(__LINE__, $data);

