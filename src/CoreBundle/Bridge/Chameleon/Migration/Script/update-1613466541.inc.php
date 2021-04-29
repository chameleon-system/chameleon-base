<h1>Build #1613466541</h1>
<h2>Date: 2021-02-16</h2>
<div class="changelog">
    - #648: Add a unified theme to backend and use in cms settings
</div>
<?php

$themeId = '5f047d9b-0c20-0bfb-2dce-f8193653965c';

// NOTE some paths may not exist (Image crop or Shop)
$data = TCMSLogChange::createMigrationQueryData('pkg_cms_theme', 'en')
    ->setFields([
        'name' => 'Backend',
        'snippet_chain' => '@ChameleonSystemCoreBundle/Resources/views
@ChameleonSystemImageCropBundle/Resources/views
@ChameleonSystemMediaManagerBundle/Resources/views
@ChameleonSystemShopBundle/Resources/views',
        'less_file' => '',
        'directory' => '',
        'cms_media_id' => '1',
        'id' => $themeId,
    ])
;
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
  ->setFields([
      'name' => 'pkg_cms_theme_id',
      'translation' => 'Backend Theme',
      'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_TABLELIST'),
      'cms_tbl_field_tab' => '',
      'isrequired' => '0',
      'fieldclass' => '',
      'modifier' => 'none',
      'field_default_value' => '',
      'length_set' => '',
      'fieldtype_config' => '',
      'restrict_to_groups' => '0',
      'field_width' => '0',
      'position' => '2180',
      '049_helptext' => 'The snippet chain of this theme is used as paths for the views of any backend module.',
      'row_hexcolor' => '',
      'is_translatable' => '0',
      'validation_regex' => '',
      'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_config'),
      'fieldclass_subtype' => '',
      'class_type' => 'Core',
      'id' => '31bc74f7-60e7-a6ef-75eb-ec418e0acec0',
  ])
;
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields([
        'translation' => 'Backend-Theme',
        '049_helptext' => 'Die Snippet-Chain dieses Themes wird zur Anzeige der Backend-Module verwendet.',
    ])
    ->setWhereEquals([
        'id' => '31bc74f7-60e7-a6ef-75eb-ec418e0acec0',
    ])
;
TCMSLogChange::update(__LINE__, $data);


TCMSLogChange::SetFieldPosition(TCMSLogChange::GetTableId('cms_config'), 'pkg_cms_theme_id', 'cms_config_cmsmodule_extensions');

$query ="ALTER TABLE `cms_config`
                        ADD `pkg_cms_theme_id` CHAR(36) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL COMMENT 'Theme: '";
TCMSLogChange::RunQuery(__LINE__, $query);

TCMSLogChange::RunQuery(__LINE__, "UPDATE `cms_config` SET `pkg_cms_theme_id` = :themeId", ['themeId' => $themeId]);

$query ="ALTER TABLE `cms_config` ADD INDEX `pkg_cms_theme_id` (`pkg_cms_theme_id`)";
TCMSLogChange::RunQuery(__LINE__, $query);

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'de')
  ->setFields([
      'only_one_record_tbl' => '0',
  ])
  ->setWhereEquals([
      'name' => 'pkg_cms_theme',
  ])
;
TCMSLogChange::update(__LINE__, $data);

TCMSLogChange::addInfoMessage(
    'The support for a "CMS Theme" has been removed in favor of "Themes" for both front- and backend. A new theme for the backend named
    "Backend" has been added and it contains the paths of the current backend modules.'
);
