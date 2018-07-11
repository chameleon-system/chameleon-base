<h1>Build #1518530903</h1>
<h2>Date: 2018-02-13</h2>
<div class="changelog">
    - Remove CMSSearchIndex
</div>
<?php

$query = 'ALTER TABLE `cms_portal` DROP INDEX `index_search`';
TCMSLogChange::RunQuery(__LINE__, $query);

$fieldId = TCMSLogChange::GetTableFieldId(TCMSLogChange::GetTableId('cms_portal'), 'index_search');

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
      '049_helptext' => 'Sie können hier angeben, ob das Portal bei Verwendung des Suchmoduls beim Starten des Spiders mit indiziert werden soll.

@deprecated since 6.2.0 - no longer used.',
  ])
  ->setWhereEquals([
      'id' => $fieldId,
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
  ->setFields([
      '049_helptext' => 'You can specify here if the portal should be indexed when the search indexer is started.

@deprecated since 6.2.0 - no longer used.',
  ])
  ->setWhereEquals([
      'id' => $fieldId,
  ])
;
TCMSLogChange::update(__LINE__, $data);

if (true === TCMSLogChange::TableExists('cms_search_index')) {
    $tableId = TCMSLogChange::GetTableId('cms_search_index');

    $data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'en')
        ->setFields([
            'notes' => 'The index for the website search
    
    @deprecated since 6.2.0 - no longer used.
    ',
        ])
        ->setWhereEquals([
            'id' => $tableId,
        ])
    ;
    TCMSLogChange::update(__LINE__, $data);

    $data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'de')
        ->setFields([
            'notes' => 'Hier wird der Index für die Webseitensuche hinterlegt
    
    @deprecated since 6.2.0 - no longer used.',
        ])
        ->setWhereEquals([
            'id' => $tableId,
        ])
    ;
    TCMSLogChange::update(__LINE__, $data);
}
