<h1>Build #1785409737</h1>
<h2>Date: 2026-07-30</h2>
<div class="changelog">
    - ref #70945: extend iso_6391 to hold 5 characters
    - ref #70945: add url prefix to cms_language
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
      'length_set' => '5', // prev.: '4'
      'position' => '3290', // prev.: '0'
  ])
  ->setWhereEquals([
      'id' => '1319',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$query ="ALTER TABLE `cms_language`
                     CHANGE `iso_6391`
                            `iso_6391` VARCHAR(5) NOT NULL COMMENT 'ISO 639-1 Sprachkürzel: Die folgenden Sprachkürzel entsprechen dem internationalen Standard ISO 639-1 und können überall dort verwendet werden, wo Sprachangaben nach RFC 1766 erforderlich sind.\\r\\n\\r\\nIn den meisten Anwendungsfällen sind diese zweistelli'";
TCMSLogChange::RunQuery(__LINE__, $query);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
      'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_language'),
      'name' => 'url_prefix',
      'translation' => 'URL-Prefix', // prev.: ''
      'position' => '3291', // prev.: '0'f
      'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_STRING'),
      'id' => '4cc5f49f-3198-4a79-ff37-dcaa7f9bdc26',
  ])
;
TCMSLogChange::insert(__LINE__, $data);

$query ="ALTER TABLE `cms_language`
                        ADD `url_prefix` VARCHAR(255) NOT NULL COMMENT 'URL-Prefix: '";
TCMSLogChange::RunQuery(__LINE__, $query);

$query ="ALTER TABLE `cms_language`
                        ADD INDEX  `cms_language_url_prefix` ( url_prefix )";
TCMSLogChange::RunQuery(__LINE__, $query);