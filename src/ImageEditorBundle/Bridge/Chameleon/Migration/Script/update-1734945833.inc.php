<h1>update - Build #1734945833</h1>
<h2>Date: 2024-12-23</h2>
<div class="changelog">
    add image editor to snippet chain<br/>
</div>
<?php

$connection = TCMSLogChange::getDatabaseConnection();

$backendThemeId = $connection->fetchOne('SELECT `pkg_cms_theme_id` FROM `cms_config`');

TCMSLogChange::addToSnippetChain('@ChameleonSystemImageEditorBundle/Resources/views', '@ChameleonSystemCoreBundle/Resources/views', [$backendThemeId]);
