<h1>Build #1591109859</h1>
<h2>Date: 2020-06-02</h2>
<div class="changelog">
    - #574: Make sure right for navigation edit exists; add right for media edit and connect to "editor"
</div>
<?php

$connection = TCMSLogChange::getDatabaseConnection();

$currentNavigationEditId = $connection->fetchColumn("SELECT `id` FROM `cms_right` WHERE `name` = 'navigation_edit'");

if (false === $currentNavigationEditId) {
    // NOTE this replicates update-1549376774.inc.php (which effects sometimes seems to be missing/broken in db)

    $navigationRightId = TCMSLogChange::createUnusedRecordId('cms_right');

    $data = TCMSLogChange::createMigrationQueryData('cms_right', 'en')
        ->setFields(
            [
                'name' => 'navigation_edit',
                '049_trans' => 'Edit site navigation',
                'id' => $navigationRightId,
            ]
        );
    TCMSLogChange::insert(__LINE__, $data);

    $data = TCMSLogChange::createMigrationQueryData('cms_right', 'de')
        ->setFields(
            [
                '049_trans' => 'Seitennavigation editieren',
            ]
        )
        ->setWhereEquals(
            [
                'id' => $navigationRightId,
            ]
        );
    TCMSLogChange::update(__LINE__, $data);

    // Assign the new right to all roles that currently have the right to edit pages.

    $query = 'SELECT `target_id` FROM `cms_tbl_conf_cms_role1_mlt` WHERE `source_id` = ?';
    $rows = $connection->fetchAll($query, [
        TCMSLogChange::GetTableId('cms_tpl_page'),
    ]);
    foreach ($rows as $row) {
        $data = TCMSLogChange::createMigrationQueryData('cms_role_cms_right_mlt', 'en')
            ->setFields([
                'source_id' => $row['target_id'],
                'target_id' => $navigationRightId,
                'entry_sort' => '0',
            ])
        ;
        TCMSLogChange::insert(__LINE__, $data);
    }
}

/*
// Now do something similar to media_edit

// TODO current menu entry uses existing "cms_image_pool_upload" for this.


$mediaRightId = TCMSLogChange::createUnusedRecordId('cms_right');

$data = TCMSLogChange::createMigrationQueryData('cms_right', 'en')
    ->setFields(
        [
            'name' => 'media_edit',
            '049_trans' => 'Edit media items',
            'id' => $mediaRightId,
        ]
    );
TCMSLogChange::insert(__LINE__, $data);

// TODO / NOTE if this fails nothing happens??

$data = TCMSLogChange::createMigrationQueryData('cms_right', 'de')
    ->setFields(
        [
            '049_trans' => 'Media-Elemente editieren',
        ]
    )
    ->setWhereEquals(
        [
            'id' => $mediaRightId,
        ]
    );
TCMSLogChange::update(__LINE__, $data);

$imagePoolRightId = $connection->fetchColumn("SELECT `id` FROM `cms_right` WHERE `name` = 'cms_image_pool_upload'");

if (false === $imagePoolRightId) {
    throw new RuntimeException('No right for cms_image_pool_upload found. It is needed for new media right.');
}

$roleRows = $connection->fetchAll("SELECT `source_id` FROM `cms_role_cms_right_mlt` WHERE `target_id` = :imagePoolRightId", ['imagePoolRightId' => $imagePoolRightId]);

foreach ($roleRows as $roleRow) {
    $data = TCMSLogChange::createMigrationQueryData('cms_role_cms_right_mlt', 'en')
        ->setFields([
            'source_id' => $roleRow['source_id'],
            'target_id' => $mediaRightId,
            'entry_sort' => '0',
        ])
    ;
    TCMSLogChange::insert(__LINE__, $data);
}

// TODO adapt rights management for menu groups!
*/
