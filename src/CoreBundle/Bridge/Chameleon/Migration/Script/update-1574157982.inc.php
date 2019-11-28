<h1>Build #1574157982</h1>
<h2>Date: 2019-11-19</h2>
<div class="changelog">
    - ref 259: make cron job execute every n-minutes field a required field
</div>
<?php
$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields(
        [
            'name' => 'execute_every_n_minutes',
            'isrequired' => '1',
        ]
    )
    ->setWhereEquals(
        [
            'id' => TCMSLogChange::GetTableFieldId(
                TCMSLogChange::GetTableId('cms_cronjobs'),
                'execute_every_n_minutes'
            ),
        ]
    );
TCMSLogChange::update(__LINE__, $data);

