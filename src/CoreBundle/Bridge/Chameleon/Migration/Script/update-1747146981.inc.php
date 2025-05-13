<h1>update - Build #1747146981</h1>
<h2>Date: 2025-05-13</h2>
<div class="changelog">
    #66586 remove deprecated pagedefs and field confs<br/>
</div>
<?php

//Remove TCMSFieldGMapCoordinate
$data = TCMSLogChange::createMigrationQueryData('cms_field_type', 'de')
    ->setWhereEquals([
        'fieldclass' => 'TCMSFieldGMapCoordinate',
    ])
;
TCMSLogChange::delete(__LINE__, $data);