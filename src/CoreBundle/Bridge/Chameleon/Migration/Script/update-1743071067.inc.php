<h1>Build #1743071067</h1>
<h2>Date: 2025-03-27</h2>
<div class="changelog">
    - delete deprecated cms document tree folders
</div>
<?php

TCMSLogChange::UpdateAutoClasses('cms_document_tree');

$namesToDelete = [
    'Datenbank Sicherungen',
    'CMS Automatische Datensicherung',
];

foreach ($namesToDelete as $name) {
    $cmsDocumentTree = TdbCmsDocumentTree::GetNewInstance();

    if ($cmsDocumentTree->LoadFromField('name', $name) || $cmsDocumentTree->LoadFromField('name__de', $name)) {
        $data = TCMSLogChange::createMigrationQueryData('cms_document_tree', 'de')
            ->setWhereEquals([
                'id' => $cmsDocumentTree->id,
            ]);
        TCMSLogChange::delete(__LINE__, $data);
    }
}
