<h1>Build #1710771613</h1>
<h2>Date: 2024-03-18</h2>
<div class="changelog">
    - ref #850: add bundle's path into backend theme's snippet chain
</div>
<?php

$connection = TCMSLogChange::getDatabaseConnection();

$backendThemeId = $connection->fetchOne('SELECT `pkg_cms_theme_id` FROM `cms_config`');

TCMSLogChange::addToSnippetChain('@ChameleonSystemNewsletterBundle/Resources/views', '@ChameleonSystemCoreBundle/Resources/views', [$backendThemeId]);
