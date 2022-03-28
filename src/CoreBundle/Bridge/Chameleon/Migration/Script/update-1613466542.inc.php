<h1>Build #1613466542</h1>
<h2>Date: 2021-03-22</h2>
<div class="changelog">
    - #764: Fix backend theme snippet chain content (it now should have ALL paths)
</div>
<?php

$connection = TCMSLogChange::getDatabaseConnection();

$backendThemeId = $connection->fetchOne('SELECT `pkg_cms_theme_id` FROM `cms_config`');

if (false !== $backendThemeId) {
    // Make sure to have the same starting point
    TCMSLogChange::removeFromSnippetChain('@ChameleonSystemCoreBundle/Resources/views', [$backendThemeId]);
    TCMSLogChange::removeFromSnippetChain('../extensions', [$backendThemeId]);

    // Add correctly ordered "default" elements (now removed from \TPkgViewRendererSnippetDirectory::getBasePaths())
    TCMSLogChange::addToSnippetChain('@ChameleonSystemCoreBundle/Resources/views', '^', [$backendThemeId]);
    TCMSLogChange::addToSnippetChain('../extensions', '@ChameleonSystemCoreBundle/Resources/views', [$backendThemeId]);
}

