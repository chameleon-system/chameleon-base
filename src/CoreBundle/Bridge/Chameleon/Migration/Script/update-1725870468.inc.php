<h1>Build #1725870468</h1>
<h2>Date: 2024-09-09</h2>
<div class="changelog">
    - #64178: Set all fields "Google Maps-Koordinaten" to new field "Maps-Koordinaten" with OpenStreetMap instead of Google Maps (SELECT BY OLD CLASSNAME)
</div>
<?php

$query = "UPDATE `cms_field_conf`
          LEFT JOIN `cms_field_type` ON `cms_field_type`.`id` = `cms_field_conf`.`cms_field_type_id`
          SET `cms_field_conf`.`cms_field_type_id` = '343b02d4-f02c-c533-be6f-f063d62bc982'
          WHERE `cms_field_type`.`fieldclass` = 'TCMSFieldGMapCoordinate'";

$connection = TCMSLogChange::getDatabaseConnection();
$connection->executeQuery($query);