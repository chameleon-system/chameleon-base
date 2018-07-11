<h1>update - Build #1496836232</h1>
<h2>Date: 2017-06-07</h2>
<div class="changelog">
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_message_manager_backend_message', 'en')
    ->setFields(
        array(
            'cms_config_id' => '1',
            'name' => 'TABLEEDITOR_DOMAIN_UNSET_PRIMARY_NOT_POSSIBLE',
            'cms_message_manager_message_type_id' => '3',
            'description' => '',
            'message' => 'The domain stays marked as primary. Mark another domain as primary domain to unset this one (exactly one primary domain per portal and language must be set anytime).',
            'id' => TCMSLogChange::createUnusedRecordId('cms_message_manager_backend_message'),
        )
    );
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_message_manager_backend_message', 'de')
    ->setFields(
        array(
            'message' => 'Die Domain ist weiterhin als primär markiert. Markieren Sie eine andere Domain als primär, dann wird diese hier den Primär-Status automatisch verlieren (es muss immer genau eine primäre Domain pro Portal und Sprache geben).',
        )
    )
    ->setWhereEquals(
        array(
            'name' => 'TABLEEDITOR_DOMAIN_UNSET_PRIMARY_NOT_POSSIBLE',
        )
    );
TCMSLogChange::update(__LINE__, $data);

$listManagerClassId = TCMSLogChange::createUnusedRecordId('cms_tbl_list_class');

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_list_class', 'en')
    ->setFields(
        array(
            'name' => 'TCMSListManagerPortalDomains',
            'classname' => 'TCMSListManagerPortalDomains',
            'cms_tbl_conf_id' => TCMSLogChange::GetTableId('cms_portal_domains'),
            'class_subtype' => '',
            'classlocation' => 'Core',
            'id' => $listManagerClassId,
        )
    );
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'en')
    ->setFields(
        array(
            'cms_tbl_list_class_id' => $listManagerClassId,
        )
    )
    ->setWhereEquals(
        array(
            'name' => 'cms_portal_domains',
        )
    );
TCMSLogChange::update(__LINE__, $data);
