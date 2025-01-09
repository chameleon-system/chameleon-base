<h1>update - Build #1735560806</h1>
<h2>Date: 2024-12-30</h2>
<div class="changelog">
    - #65182: add routing for dashboard widget API
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('pkg_cms_routing', 'de')
    ->setFields([
        'name' => 'CMS Dashboard Widget API',
        'short_description' => 'Export Endpunkte für Dashboard Widgets (backend)',
        'type' => 'yaml',
        'resource' => '@ChameleonSystemCmsDashboardBundle/Resources/config/routing.yml',
        'position' => '6',
        'system_page_name' => '',
        'active' => '1',
        'id' => '4ce4139d-9beb-41e5-9f0f-8cda96d4936b',
    ]);
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('pkg_cms_routing', 'en')
    ->setFields(
        [
            'name' => 'CMS Dashboard Widget API',
            'short_description' => 'Export Endpoints for Dashboard Widgets (backend)',
        ]
    )
    ->setWhereEquals(
        [
            'id' => '4ce4139d-9beb-41e5-9f0f-8cda96d4936b',
        ]
    );
TCMSLogChange::update(__LINE__, $data);