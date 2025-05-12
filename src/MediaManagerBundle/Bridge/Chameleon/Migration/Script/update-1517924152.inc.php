<h1>update - Build #1517924152</h1>
<h2>Date: 2018-02-06</h2>
<div class="changelog">
    - add table editors<br/>
</div>
<?php
$databaseConnection = TCMSLogChange::getDatabaseConnection();
$tableEditorClass = $databaseConnection->fetchOne(
    'SELECT `table_editor_class` FROM `cms_tbl_conf` WHERE `name` = \'cms_media\''
);
if ('TCMSTableEditorMedia' === $tableEditorClass) {
    $data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'de')
        ->setFields(
            [
                'table_editor_class' => '\ChameleonSystem\MediaManagerBundle\Bridge\Chameleon\TableEditor\CmsMediaTableEditor',
            ]
        )
        ->setWhereEquals(
            [
                'name' => 'cms_media',
            ]
        );
    TCMSLogChange::update(__LINE__, $data);
} else {
    TCMSLogChange::addInfoMessage(
        sprintf(
            'The table editor class for the table cms_media is %s, please extend from `\ChameleonSystem\MediaManagerBundle\Bridge\Chameleon\TableEditor\CmsMediaTableEditor` in that class.',
            $tableEditorClass
        ),
        TCMSLogChange::INFO_MESSAGE_LEVEL_TODO
    );
}

$tableEditorClassTree = $databaseConnection->fetchOne(
    'SELECT `table_editor_class` FROM `cms_tbl_conf` WHERE `name` = \'cms_media_tree\''
);
if ('TCMSTableEditorMediaTree' === $tableEditorClassTree) {
    $data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'de')
        ->setFields(
            [
                'table_editor_class' => '\ChameleonSystem\MediaManagerBundle\Bridge\Chameleon\TableEditor\CmsMediaMediaTableEditor',
            ]
        )
        ->setWhereEquals(
            [
                'name' => 'cms_media_tree',
            ]
        );
    TCMSLogChange::update(__LINE__, $data);
} else {
    TCMSLogChange::addInfoMessage(
        sprintf(
            'The table editor class for the table cms_media_tree is %s, please extend from `\ChameleonSystem\MediaManagerBundle\Bridge\Chameleon\TableEditor\CmsMediaMediaTableEditor` in that class.',
            $tableEditorClass
        ),
        TCMSLogChange::INFO_MESSAGE_LEVEL_TODO
    );
}
