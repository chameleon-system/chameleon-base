<h1>Build #1586154706</h1>
<h2>Date: 2020-04-06</h2>
<div class="changelog">
    - #573: Remove right navigation_edit from editor
</div>
<?php

$connection = TCMSLogChange::getDatabaseConnection();

$editorRoleId = $connection->fetchColumn("SELECT `id` FROM `cms_role` WHERE `name` = 'editor'");
$navigationEditRightId = $connection->fetchColumn("SELECT `id` FROM `cms_right` WHERE `name` = 'navigation_edit'");

if (false === $editorRoleId || false === $navigationEditRightId) {
    return;
}

$result = $connection->executeQuery(
        "DELETE FROM `cms_role_cms_right_mlt` WHERE `source_id` = :editorRoleId AND `target_id` = :navigationEditRightId",
        [
            'editorRoleId' => $editorRoleId,
            'navigationEditRightId' => $navigationEditRightId
        ]);

if ($result->columnCount() > 0) {
    TCMSLogChange::addInfoMessage("Backend user right 'navigation_edit' was removed from role 'editor'.", TCMSLogChange::INFO_MESSAGE_LEVEL_WARNING);
}
