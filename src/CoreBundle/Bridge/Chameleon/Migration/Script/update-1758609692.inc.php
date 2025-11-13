<h1>update - Build #1758609692</h1>
<h2>Date: 2025-10-16</h2>
<div class="changelog">
    - #67927: drop index from metatags field in cms_media table
</div>
<?php

$indexExists = TCMSLogChange::getDatabaseConnection()->fetchOne("
    SELECT COUNT(1)
    FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = ?
    AND INDEX_NAME = ?
", ['cms_media', 'metatags']);

if ($indexExists) {
    $query = "ALTER TABLE cms_media DROP INDEX metatags";
    TCMSLogChange::RunQuery(__LINE__, $query);
}
