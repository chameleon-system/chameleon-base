<h1>Build #1551195244</h1>
<h2>Date: 2019-02-26</h2>
<div class="changelog">
    - #275: Remove usages of TCMSFieldWorkflowBool
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
        'fieldclass' => 'TCMSFieldBoolean',
    ])
    ->setWhereEquals([
        'fieldclass' => 'TCMSFieldWorkflowBool',
    ]);
TCMSLogChange::update(__LINE__, $data);
