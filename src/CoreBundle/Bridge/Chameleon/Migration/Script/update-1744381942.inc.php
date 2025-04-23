<h1>Build #1744381941</h1>
<h2>Date: 2025-04-22</h2>
<div class="changelog">
    - ref #66343: enhance all field descriptions in cms_field_conf – replace http://, convert URLs to markdown
</div>
<?php

$dbConnection = \ChameleonSystem\CoreBundle\ServiceLocator::get('database_connection');

$baseLanguage = \TdbCmsConfig::GetInstance()?->GetFieldTranslationBaseLanguage();

$englishFieldName = '049_helptext__en';
$germanFieldName = '049_helptext';
if ('en' === $baseLanguage->fieldIso6391) {
    $germanFieldName = '049_helptext__de';
    $englishFieldName = '049_helptext';
}

// replace http:// with https:// in cms_field_conf help texts
foreach ([$germanFieldName, $englishFieldName] as $fieldName) {
    $query = "UPDATE `cms_field_conf` 
              SET `$fieldName` = REPLACE(`$fieldName`, 'http://', 'https://')
              WHERE `$fieldName` LIKE '%http://%';";
    TCMSLogChange::RunQuery(__LINE__, $query);
}
