<h1>Build #1540997772</h1>
<h2>Date: 2018-10-31</h2>
<div class="changelog">
    - #117: Add routing configuration for CSS generation
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('pkg_cms_routing', 'en')
    ->setFields([
        'name' => 'chameleon_system_view_renderer.generated_css',
        'short_description' => '',
        'type' => 'service',
        'resource' => 'chameleon_system_view_renderer.routing.generate_css_route_collection_generator',
        'position' => '4',
        'system_page_name' => '',
        'id' => TCMSLogChange::createUnusedRecordId('pkg_cms_routing'),
    ])
;
TCMSLogChange::insert(__LINE__, $data);
