<h1>Build #1742210913</h1>
<h2>Date: 2025-03-17</h2>
<div class="changelog">
    - ref #66056: removed translateable from name field of table cms_right
</div>
<?php

/** @var ChameleonSystem\CoreBundle\Util\FieldTranslationUtil $fieldTranslationHelper */
$fieldTranslationHelper = ChameleonSystem\CoreBundle\ServiceLocator::get(
    'chameleon_system_core.util.field_translation'
);

$connection = TCMSLogChange::getDatabaseConnection();

foreach ($connection->fetchAllAssociative('SELECT * FROM cms_right WHERE name=""') as $cmsRight) {
    $name = $cmsRight['name'];
    if (isset($cmsRight['name__en'])) {
        $name = $cmsRight['name__en'];
    }
    // DE-Translation has a higher priority
    if (isset($cmsRight['name__de'])) {
        $name = $cmsRight['name__de'];
    }
    $connection->update('cms_right', ['name' => $name], ['id' => $cmsRight['id']]);
}

TCMSLogChange::makeFieldMonolingual('cms_right', 'name');
