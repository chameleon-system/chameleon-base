<h1>Build #1530173980</h1>
<h2>Date: 2018-06-28</h2>
<div class="changelog">
    - Remove SmartUrlHandler for CSS compilation (gets replaced by routing).
</div>
<?php

$databaseConnection = TCMSLogChange::getDatabaseConnection();

$oldUrlHandlerId = $databaseConnection->fetchColumn("SELECT `id` FROM `cms_smart_url_handler` WHERE `name` = 'TPkgViewRenderer_TCMSSmartURLHandler_SnippetLessCompiler'");
if (false === $oldUrlHandlerId) {
    return;
}

$data = TCMSLogChange::createMigrationQueryData('cms_smart_url_handler', 'en')
    ->setWhereEquals([
        'id' => $oldUrlHandlerId,
    ]);
TCMSLogChange::delete(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_smart_url_handler_cms_portal_mlt', 'en')
    ->setWhereEquals([
        'source_id' => $oldUrlHandlerId,
    ]);
TCMSLogChange::delete(__LINE__, $data);

