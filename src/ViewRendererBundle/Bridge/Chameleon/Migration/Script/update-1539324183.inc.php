<h1>update - Build #1539324183</h1>
<h2>Date: 2018-10-12</h2>
<div class="changelog">
    - #117: Only use necessary additional cache settings
</div>
<?php

$connection = TCMSLogChange::getDatabaseConnection();
$cachePathSettings = trim($connection->fetchColumn('SELECT `additional_files_to_delete_from_cache` FROM `cms_config`'));
$pathToRemove = 'chameleon/outbox/static/less/cached';

// Code copied from SnippetChainModifier:

$quotedAfterThisPath = preg_quote($pathToRemove, '#');
$pattern = '#(\s+|^)'.$quotedAfterThisPath.'(\s+|$)#';
$cachePathSettings = preg_replace($pattern, "\n", $cachePathSettings);
$cachePathSettings = preg_replace('#\s+#', "\n", $cachePathSettings);
$cachePathSettings = trim($cachePathSettings);

$data = TCMSLogChange::createMigrationQueryData('cms_config', 'de')
  ->setFields(array(
      'additional_files_to_delete_from_cache' => $cachePathSettings,
  ))
  ->setWhereEquals(array(
      'id' => '1',
  ))
;
TCMSLogChange::update(__LINE__, $data);

TCMSLogChange::addInfoMessage('The path for generated CSS can now be configured using bundle config
chameleon_system_view_renderer: css_dir (formerly the path was fixed to web/chameleon/outbox/static/less).');

TCMSLogChange::addInfoMessage('Removed "chameleon/outbox/static/less/cached" from additionally to clean cache directories.
It is now handled as part of the system.');
