<h1>update - Build #1488983306</h1>
<h2>Date: 2017-03-08</h2>
<div class="changelog">
    #38232<br/>
    make description field required<br>
</div>
<?php
$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields(
        [
            'isrequired' => '1',
        ]
    )
    ->setWhereEquals(
        [
            'id' => TCMSLogChange::GetTableFieldId(TCMSLogChange::GetTableId('cms_media'), 'description'),
        ]
    );
TCMSLogChange::update(__LINE__, $data);
