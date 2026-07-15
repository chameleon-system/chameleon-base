<h1>Build #1782994914</h1>
<h2>Date: 2026-07-02</h2>
<div class="changelog">
    - #70119: add optional URL suffix field for portal domains
</div>
<?php

$connection = TCMSLogChange::getDatabaseConnection();
$tableId = TCMSLogChange::GetTableId('cms_portal_domains');
$fieldId = 'bf529c04-2fed-4e48-8c85-c8205127f8ea';

if (false === $connection->fetchOne("SHOW COLUMNS FROM `cms_portal_domains` WHERE `Field` = 'url_suffix'")) {
    $query = "ALTER TABLE `cms_portal_domains`
                   ADD `url_suffix` VARCHAR(64) NOT NULL DEFAULT '' COMMENT 'Optional URL suffix'";
    TCMSLogChange::RunQuery(__LINE__, $query);

    $query = "ALTER TABLE `cms_portal_domains`
                   ADD INDEX `idx_portal_name_url_suffix` (`cms_portal_id`, `name`, `url_suffix`),
                   ADD INDEX `idx_portal_sslname_url_suffix` (`cms_portal_id`, `sslname`, `url_suffix`)";
    TCMSLogChange::RunQuery(__LINE__, $query);
}

if (false === $connection->fetchOne(
    'SELECT `id` FROM `cms_field_conf` WHERE `cms_tbl_conf_id` = :tableId AND `name` = :fieldName',
    ['tableId' => $tableId, 'fieldName' => 'url_suffix']
)) {
    $data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
        ->setFields([
            'cms_tbl_conf_id' => $tableId,
            'name' => 'url_suffix',
            'translation' => 'URL-Suffix',
            'cms_field_type_id' => TCMSLogChange::GetFieldType('CMSFIELD_STRING'),
            'cms_tbl_field_tab' => '',
            'isrequired' => '0',
            'fieldclass' => '',
            'fieldclass_subtype' => '',
            'class_type' => 'Core',
            'modifier' => 'none',
            'field_default_value' => '',
            'length_set' => '64',
            'fieldtype_config' => '',
            'restrict_to_groups' => '0',
            'field_width' => '0',
            'position' => '0',
            '049_helptext' => 'Optionaler URL-Suffix für diese Domain-Sprachvariante. Ohne führenden oder abschließenden Slash, z. B. fr für /fr/. Leer lassen, wenn diese Domain-Variante ohne Sprachsuffix erreichbar sein soll.',
            'row_hexcolor' => '',
            'is_translatable' => '0',
            'validation_regex' => '^[a-z0-9]+(?:[-_][a-z0-9]+)*$',
            'id' => $fieldId,
        ]);
    TCMSLogChange::insert(__LINE__, $data);

    $data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
        ->setFields([
            'translation' => 'URL suffix',
            '049_helptext' => 'Optional URL suffix for this domain language variant. Enter it without leading or trailing slashes, for example fr for /fr/. Leave empty if this domain variant should stay reachable without a language suffix.',
        ])
        ->setWhereEquals([
            'id' => $fieldId,
        ]);
    TCMSLogChange::update(__LINE__, $data);

    TCMSLogChange::SetFieldPosition($tableId, 'url_suffix', 'sslname');
}

$backendMessages = [
    'TABLEEDITOR_DOMAIN_URL_SUFFIX_REQUIRES_LANGUAGE' => [
        'id' => 'f0c7d0b2-b845-4fab-a06c-6dc3da797637',
        'de' => 'Ein URL-Suffix kann nur gesetzt werden, wenn für die Domain auch eine Sprache ausgewählt ist.',
        'en' => 'A URL suffix can only be set if a language is selected for the domain.',
    ],
    'TABLEEDITOR_DOMAIN_URL_SUFFIX_NOT_UNIQUE' => [
        'id' => '05bdb820-82d2-494d-9a77-87712d5c9447',
        'de' => 'Die Kombination aus Portal, Domain-Host und URL-Suffix ist bereits vorhanden.',
        'en' => 'The combination of portal, domain host, and URL suffix already exists.',
    ],
    'TABLEEDITOR_DOMAIN_URL_SUFFIX_PORTAL_IDENTIFIER_CONFLICT' => [
        'id' => 'ca5278f6-8562-429d-9460-e09034bc23b0',
        'de' => 'Der URL-Suffix darf nicht identisch mit dem Portal-Identifier sein.',
        'en' => 'The URL suffix must not match the portal identifier.',
    ],
];

foreach ($backendMessages as $messageName => $messageData) {
    if (false === $connection->fetchOne(
        'SELECT `id` FROM `cms_message_manager_backend_message` WHERE `name` = :name',
        ['name' => $messageName]
    )) {
        $data = TCMSLogChange::createMigrationQueryData('cms_message_manager_backend_message', 'de')
            ->setFields([
                'cms_config_id' => '1',
                'name' => $messageName,
                'cms_message_manager_message_type_id' => '3',
                'description' => '',
                'message' => $messageData['de'],
                'id' => $messageData['id'],
            ]);
        TCMSLogChange::insert(__LINE__, $data);

        $data = TCMSLogChange::createMigrationQueryData('cms_message_manager_backend_message', 'en')
            ->setFields([
                'message' => $messageData['en'],
            ])
            ->setWhereEquals([
                'id' => $messageData['id'],
            ]);
        TCMSLogChange::update(__LINE__, $data);
    }
}
