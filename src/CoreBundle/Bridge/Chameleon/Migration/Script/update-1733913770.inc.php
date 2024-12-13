<h1>Build #1733913770</h1>
<h2>Date: 2024-12-11</h2>
<div class="changelog">
    - ref #65259: remove MTLogin class extension
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('pkg_cms_class_manager', 'de')
    ->setWhereEquals([
        'id' => 'd01fff77-7431-ccad-f682-1cbccd36bace',
    ])
;
TCMSLogChange::delete(__LINE__, $data);

TCMSLogChange::UpdateVirtualNonDbClasses();