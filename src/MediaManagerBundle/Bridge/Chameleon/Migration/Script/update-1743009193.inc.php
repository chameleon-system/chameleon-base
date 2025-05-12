<h1>Build #1743009193</h1>
<h2>Date: 2025-03-26</h2>
<div class="changelog">
    - add overlay view for media manager module
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_tpl_module', 'de')
  ->setFields([
      // 'name' => 'Medienverwaltung Backendmodul',
      'view_mapper_config' => 'full=mediaManager/module/full.html.twig
overlay=mediaManager/module/overlay.html.twig
', // prev.: 'full=mediaManager/module/full.html.twig'
  ])
  ->setWhereEquals([
      'classname' => 'chameleon_system_media_manager.backend_module.media_manager',
  ])
;
TCMSLogChange::update(__LINE__, $data);
