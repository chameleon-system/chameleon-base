<h1>Build #1723198752</h1>
<h2>Date: 2024-08-09</h2>
<div class="changelog">
    - #64178: Set all fields "Google Maps-Koordinaten" to new field "Maps-Koordinaten" with OpenStreetMap instead of Google Maps
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields([
        //set to Maps-Koordinaten (FieldGeoCoordinates)
        'cms_field_type_id' => '343b02d4-f02c-c533-be6f-f063d62bc982',
    ])
    ->setWhereEquals([
        //all Google Maps-Koordinaten (TCMSFieldGMapCoordinate)
        'cms_field_type_id' => '2659ecf0-e16c-3c3f-1ec8-eba7d8887417'
    ])
;
TCMSLogChange::update(__LINE__, $data);
