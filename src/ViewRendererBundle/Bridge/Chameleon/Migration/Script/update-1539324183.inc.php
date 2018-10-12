<h1>update - Build #1539324183</h1>
<h2>Date: 2018-10-12</h2>
<div class="changelog">
    - #117: Only use necessary additional cache settings
</div>
<?php

$connection = TCMSLogChange::getDatabaseConnection();
$oldValue = trim($connection->fetchColumn('SELECT `additional_files_to_delete_from_cache` FROM `cms_config`'));
$newValue = getSnippetChainWithElementRemoved($oldValue, 'chameleon/outbox/static/less/cached');

$data = TCMSLogChange::createMigrationQueryData('cms_config', 'de')
  ->setFields(array(
      'additional_files_to_delete_from_cache' => $newValue,
  ))
  ->setWhereEquals(array(
      'id' => '1',
  ))
;
TCMSLogChange::update(__LINE__, $data);

// Helpers copied from SnippetChainModifier:
function optimizeSnippetChainString(string $snippetChain): string
{
    $snippetChain = preg_replace('#\s+#', "\n", $snippetChain);
    $snippetChain = trim($snippetChain);

    return $snippetChain;
}

function getSnippetChainWithElementRemoved(string $snippetChain, string $pathToRemove)
{
    $quotedAfterThisPath = preg_quote($pathToRemove, '#');
    $pattern = '#(\s+|^)'.$quotedAfterThisPath.'(\s+|$)#';
    $snippetChain = preg_replace($pattern, "\n", $snippetChain);

    return optimizeSnippetChainString($snippetChain);
}