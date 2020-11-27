<h1>Build #1606404695</h1>
<h2>Date: 2020-11-26</h2>
<div class="changelog">
    - ref #661: Add route for cross domain user impersonation
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('pkg_cms_routing', 'de')
  ->setFields([
      'name' => 'chameleon_system_extranet.login_by_tranfer_token.route_generator',
      'short_description' => '',
      'type' => 'service',
      'resource' => 'chameleon_system_extranet.login_by_tranfer_token.route_generator',
      'position' => '5',
      'system_page_name' => '',
      'active' => '1',
      'id' => '7c44194a-791e-2ba9-9b80-6fa1627d8d01',
  ]);
;
TCMSLogChange::insert(__LINE__, $data);

