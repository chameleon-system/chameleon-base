<h1>Build #1743071067</h1>
<h2>Date: 2025-03-27</h2>
<div class="changelog">
    - delete deprecated cms document tree folders
</div>
<?php

use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\FieldTranslationUtil;

TCMSLogChange::UpdateAutoClasses('cms_document_tree');

$namesToDelete = [
    'Datenbank Sicherungen',
    'CMS Automatische Datensicherung',
];

/** @var FieldTranslationUtil $util */
$util = ServiceLocator::get('chameleon_system_core.util.field_translation');

$germanLanguage = TdbCmsLanguage::GetNewInstance();
$germanLanguage->LoadFromField('iso_6391', 'de');

foreach ($namesToDelete as $nameToDelete) {
    $cmsDocumentTree = TdbCmsDocumentTree::GetNewInstance();
    $cmsDocumentTree->SetLanguage($germanLanguage->id);

    if ($cmsDocumentTree->LoadFromField('name', $nameToDelete)) {
        $data = TCMSLogChange::createMigrationQueryData('cms_document_tree', 'de')
            ->setWhereEquals([
                'id' => $cmsDocumentTree->id,
            ]);
        TCMSLogChange::delete(__LINE__, $data);
    }
}
