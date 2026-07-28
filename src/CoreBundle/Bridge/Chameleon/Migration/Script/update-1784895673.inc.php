<h1>Build #1784895673</h1>
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
          '049_helptext' => 'Ist für das Portal die präfixbasierte Mehrsprachigkeit aktiviert, können die hier für die Domain hinterlegten Sprachen über ein URL-Präfix aufgerufen werden.', // prev.: ''
      'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_portal_domains'),
      'id' => '2a0c5d0e-b1fb-0f4d-8c87-3f78ac357b74',
  ])
;
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
        ->setFields([
                'translation' => 'Additional languages', // prev.: ''
                '049_helptext' => 'If prefix-based multilingual support has been enabled for the portal, the languages configured here can be accessed for the domain via a URL prefix.', // prev.: ''
        ])->setWhereEquals(['id' => '2a0c5d0e-b1fb-0f4d-8c87-3f78ac357b74'])
;
TCMSLogChange::update(__LINE__, $data);




TCMSLogChange::SetFieldPosition(TCMSLogChange::GetTableId('cms_portal_domains'), 'cms_language_mlt', 'cms_language_id');

$query ="CREATE TABLE `cms_portal_domain_cms_language_mlt` (
                  `source_id` CHAR( 36 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ,
                  `target_id` CHAR( 36 ) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL ,
                  `entry_sort` int(11) NOT NULL default '0',
                  PRIMARY KEY ( `source_id` , `target_id` ),
                  INDEX (target_id),
                  INDEX (entry_sort)
                ) ENGINE = InnoDB";
TCMSLogChange::RunQuery(__LINE__, $query);