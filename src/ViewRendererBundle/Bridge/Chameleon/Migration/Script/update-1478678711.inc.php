<h1>update - Build #1478678711</h1>
<h2>Date: 2016-11-09</h2>
<div class="changelog">
    - #36048: Use less compiler's own caching
</div>
<?php

$connection = TCMSLogChange::getDatabaseConnection();
$oldValue = trim($connection->fetchColumn('SELECT `additional_files_to_delete_from_cache` FROM `cms_config`'));
if ('' === $oldValue) {
    $newValue = 'chameleon/outbox/static/less/cached';
} else {
    $newValue = $oldValue."\n".'chameleon/outbox/static/less/cached';
}

  $data = TCMSLogChange::createMigrationQueryData('cms_config', 'de')
      ->setFields(array(
          'additional_files_to_delete_from_cache' => $newValue,
      ))
      ->setWhereEquals(array(
          'id' => '1',
      ))
  ;
  TCMSLogChange::update(__LINE__, $data);
