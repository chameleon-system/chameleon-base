<h1>update - Build #1735560805</h1>
<h2>Date: 2024-12-30</h2>
<div class="changelog">
    - #65182: add bundle to backend theme
</div>
<?php

$connection = TCMSLogChange::getDatabaseConnection();

$backendThemeId = $connection->fetchOne('SELECT `pkg_cms_theme_id` FROM `cms_config`');

if (false !== $backendThemeId) {
    TCMSLogChange::addToSnippetChain('@ChameleonSystemCmsDashboardBundle/Resources/views', '@ChameleonSystemCoreBundle/Resources/views', [$backendThemeId]);
}
