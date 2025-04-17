<h1>Build #1744381935</h1>
<h2>Date: 2025-04-11</h2>
<div class="changelog">
    -
</div>
<?php

$dbConnection = \ChameleonSystem\CoreBundle\ServiceLocator::get('database_connection');

$filename = __DIR__.'/data/enhanced_cms_field_conf_descriptions.csv';
$data = [];

if (($handle = fopen($filename, 'rb')) !== false) {
    // "name";"table_name";"049_helptext__de_new";"049_helptext__en_new"
    $headers = fgetcsv($handle, 0, ';');
    while (($row = fgetcsv($handle, 0, ';')) !== false) {
        $data[] = array_combine($headers, $row);
    }
    fclose($handle);
}

$baseLanguage = \TdbCmsConfig::GetInstance()?->GetFieldTranslationBaseLanguage();

$englishFieldName = '049_helptext__en';
$germanFieldName = '049_helptext';
if ('en' === $baseLanguage->fieldIso6391) {
    $germanFieldName = '049_helptext__de';
    $englishFieldName = '049_helptext';
}

foreach ($data as $entry) {
    try {
        $tableId = TCMSLogChange::GetTableId($entry['table_name']);
    } catch (Exception $e) {
        continue;
    }

    $dbConnection->update(
        'cms_field_conf',
        [
            $englishFieldName => $entry['049_helptext__en_new'],
            $germanFieldName => $entry['049_helptext__de_new'],
        ],
        ['cms_tbl_conf_id' => $tableId, 'name' => $entry['name']]
    );
}
