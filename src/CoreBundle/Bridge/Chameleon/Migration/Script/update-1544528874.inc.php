<h1>Build #1544528874</h1>
<h2>Date: 2018-12-11</h2>
<div class="changelog">
    - Set English name for tree root.
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_tree', 'en')
    ->setFields([
        'name' => 'Websites',
    ])
    ->setWhereEquals([
        'id' => '99', // ID can be hard-coded, because we rely on this ID anyway in CMSTreeNodeSelect.
        'name' => '',
    ]);
TCMSLogChange::update(__LINE__, $data);
