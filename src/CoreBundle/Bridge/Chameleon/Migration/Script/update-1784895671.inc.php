<h1>Build #1784895671</h1>
<h2>Date: 2026-07-24</h2>
<div class="changelog">
    - 70643 add additional domains
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
      // 'name' => 'cms_language_mlt',
          'fieldtype_config' => 'mltTableName=cms_portal_domain_cms_language_mlt
bOpenOnLoad=true', // prev.: 'mltTableName=cms_portal_domain_cms_language_mlt'
          'name' => 'cms_language_mlt', // prev.: 'cms_portal_domains_mlt'
          'translation' => 'Weitere Sprachen', // prev.: ''
          'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_MULTITABLELIST'), // prev.: '46'
          'position' => '3294', // prev.: '0'
          '049_helptext' => 'Wenn für das Portal prefixbasierte Mehrsprachigkeit aktiviert wurde, dann werden die hier hinterlegten Sprachen für die domain via URL-Prefix ansteuerbar.', // prev.: ''
      'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_portal_domains'),
      'id' => '2a0c5d0e-b1fb-0f4d-8c87-3f78ac357b74',
  ])
;
TCMSLogChange::insert(__LINE__, $data);

$query ="CREATE TABLE `cms_portal_domain_cms_language_mlt` (
                  `source_id` CHAR( 36 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ,
                  `target_id` CHAR( 36 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ,
                  `entry_sort` int(11) NOT NULL default '0',
                  PRIMARY KEY ( `source_id` , `target_id` ),
                  INDEX (target_id),
                  INDEX (entry_sort)
                ) ENGINE = InnoDB";
TCMSLogChange::RunQuery(__LINE__, $query);