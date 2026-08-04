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
    ])
    ->setWhereEquals([
      'id' => TCMSLogChange::GetTableFieldId(TCMSLogChange::getTableId('cms_language'), 'iso_6391'),
    ])
;
TCMSLogChange::update(__LINE__, $data);

$query = "ALTER TABLE `cms_language`
                     CHANGE `iso_6391`
                            `iso_6391` VARCHAR(5) NOT NULL COMMENT 'ISO 639-1 Sprachkürzel: Die folgenden Sprachkürzel entsprechen dem internationalen Standard ISO 639-1 und können überall dort verwendet werden, wo Sprachangaben nach RFC 1766 erforderlich sind.\\r\\n\\r\\nIn den meisten Anwendungsfällen sind diese zweistelli'";
TCMSLogChange::RunQuery(__LINE__, $query);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields([
      'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_language'),
      'name' => 'url_prefix',
      'translation' => 'URL-Prefix', // prev.: ''
      'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_STRING'),
      '049_helptext' => 'Optionaler URL-Präfix für diese Sprache. Wenn ein Wert gepflegt ist, wird dieser für die Generierung von Sprach-URLs verwendet, z. B. "fr" oder "it". Ist das Feld leer, wird stattdessen der Wert aus "iso_6391" als Fallback verwendet, wenn die Prefixbasierte Mehrsprachigkeit aktiviert ist!',
      'id' => '4cc5f49f-3198-4a79-ff37-dcaa7f9bdc26',
    ])
;
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
            'translation' => 'URL-Prefix',
            '049_helptext' => 'Optional URL prefix for this language. If a value is set, it will be used to generate language-specific URLs, for example "fr" or "it". If the field is empty, the value from "iso_6391" will be used as a fallback when prefix-based multilingual routing is enabled.',
    ])
    ->setWhereEquals([
            'id' => '4cc5f49f-3198-4a79-ff37-dcaa7f9bdc26',
    ]);
TCMSLogChange::update(__LINE__, $data);
TCMSLogChange::SetFieldPosition(TCMSLogChange::GetTableId('cms_language'), 'url_prefix', 'iso_6391');

$query = "ALTER TABLE `cms_language`
                        ADD `url_prefix` VARCHAR(255) NOT NULL COMMENT 'URL-Prefix: '";
TCMSLogChange::RunQuery(__LINE__, $query);

$query = 'ALTER TABLE `cms_language`
                        ADD INDEX  `cms_language_url_prefix` ( url_prefix )';
TCMSLogChange::RunQuery(__LINE__, $query);

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf_index', 'de')
    ->setFields([
            'id' => '2392ef6f-ef78-dc86-2016-dc1f4024bfcb',
            'definition' => 'url_prefix',
            'name' => 'cms_language_url_prefix',
            'type'            => 'INDEX',
            'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_language'),
    ])
;
TCMSLogChange::insert(__LINE__, $data);

$query = 'ALTER TABLE `cms_language`
                        ADD INDEX  `cms_language_iso_6391` ( iso_6391 )';
TCMSLogChange::RunQuery(__LINE__, $query);

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf_index', 'de')
    ->setFields([
            'id' => 'c87a40bc-bc33-cf1e-1237-72b53324cf25',
            'definition' => 'iso_6391',
            'name' => 'cms_language_iso_6391',
            'type'            => 'INDEX',
            'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_language'),
    ])
;
TCMSLogChange::insert(__LINE__, $data);
