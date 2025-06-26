<h1>update - Build #1750860100</h1>
<h2>Date: 2025-06-26</h2>
<div class="changelog">
    - ref #66942: bugfix cms_user_cms_language contains wrong formats
</div>

<?php
if(false === TCMSLogChange::TableExists('cms_user_cms_language_mlt')){
    return;
}
$dbConnection = TCMSLogChange::getDatabaseConnection();

// Fetch all entries where target_id is 'de' or 'en'
$query = "SELECT source_id, target_id, entry_sort FROM cms_user_cms_language_mlt WHERE target_id IN ('de', 'en')";
$result = $dbConnection->fetchAllAssociative($query);

foreach ($result as $row) {
    $tdbCmsLanguage = \TdbCmsLanguage::GetNewInstance();
    if(false === $tdbCmsLanguage->LoadFromField('iso_6391',$row['target_id'])){
        continue;
    }

    $newTargetId = $tdbCmsLanguage->id;

    $dbConnection->executeQuery(
        'UPDATE cms_user_cms_language_mlt SET target_id = ? WHERE source_id = ? AND target_id = ?',
        [$newTargetId, $row['source_id'], $row['target_id']]
    );
}