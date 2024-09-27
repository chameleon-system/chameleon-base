<h1>Build #1703671912</h1>
<h2>Date: 2023-12-27</h2>
<div class="changelog">
    - ref #59178 - add auto class bundle to backend theme
</div>
<?php

TCMSLogChange::requireBundleUpdates('ChameleonSystemCoreBundle', 1708346253);

$query = "SELECT pkg_cms_theme_id FROM cms_config";
$themeId = TCMSLogChange::getDatabaseConnection()->fetchOne($query);

$theme = TCMSLogChange::getDatabaseConnection()->fetchAssociative('SELECT * FROM `pkg_cms_theme` WHERE `id` = :themeId', ['themeId' => $themeId]);
$theme['snippet_chain'] = trim($theme['snippet_chain']) . "\n" . '@ChameleonSystemAutoclassesBundle/Resources/views';

$data = TCMSLogChange::createMigrationQueryData('pkg_cms_theme', 'de')
  ->setFields($theme)
  ->setWhereEquals([
      'id' => $theme['id'],
  ])
;
TCMSLogChange::update(__LINE__, $data);

