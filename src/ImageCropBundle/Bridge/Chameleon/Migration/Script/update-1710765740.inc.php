<h1>Build #1710765740</h1>
<h2>Date: 2024-03-18</h2>
<div class="changelog">
    - ref #843: add image crop bundle's path into backend theme's snippet chain
</div>
<?php

$connection = TCMSLogChange::getDatabaseConnection();

$backendThemeId = $connection->fetchOne('SELECT `pkg_cms_theme_id` FROM `cms_config`');

TCMSLogChange::addToSnippetChain('@ChameleonSystemImageCropBundle/Resources/views', '@ChameleonSystemCoreBundle/Resources/views', [$backendThemeId]);
