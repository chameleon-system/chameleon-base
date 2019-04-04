<h1>Build #1551195244</h1>
<h2>Date: 2019-02-26</h2>
<div class="changelog">
    - #275: Remove usages of workflow-related field classes
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
        'fieldclass' => 'TCMSFieldLookup',
    ])
    ->setWhereEquals([
        'fieldclass' => 'TCMSFieldWorkflowActionType',
    ]);
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
        'fieldclass' => 'TCMSFieldBoolean',
    ])
    ->setWhereEquals([
        'fieldclass' => 'TCMSFieldWorkflowBool',
    ]);
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
        'fieldclass' => 'TCMSFieldVarchar',
    ])
    ->setWhereEquals([
        'fieldclass' => 'TCMSFieldWorkflowAffectedRecord',
    ]);
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
        'fieldclass' => 'TCMSFieldBoolean',
    ])
    ->setWhereEquals([
        'fieldclass' => 'TCMSFieldWorkflowPublishActive',
    ]);
TCMSLogChange::update(__LINE__, $data);
