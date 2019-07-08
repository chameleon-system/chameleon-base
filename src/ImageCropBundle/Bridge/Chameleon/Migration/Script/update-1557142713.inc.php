<h1>Build #1557142713</h1>
<h2>Date: 2019-05-06</h2>
<div class="changelog">
    - display notice for translatable "image with crop" fields
</div>
<?php
$query = "SELECT `cms_field_conf`.`name` AS fieldName, `cms_tbl_conf`.`name` AS tableName 
          FROM `cms_field_conf` 
            INNER JOIN `cms_field_type` ON `cms_field_conf`.`cms_field_type_id` = `cms_field_type`.`id` 
            INNER JOIN `cms_tbl_conf` ON `cms_field_conf`.`cms_tbl_conf_id` = `cms_tbl_conf`.`id` 
          WHERE `cms_field_conf`.`is_translatable` = '1' AND `cms_field_type`.`constname` = 'CMSFIELD_EXTENDEDTABLELIST_MEDIA_CROP'";

$translatableFields = TCMSLogChange::getDatabaseConnection()->fetchAll($query);
if (count($translatableFields) > 0) {
    $fieldsString = implode(
        ', ',
        array_reduce(
            $translatableFields,
            function (array $carry, $item) {
                $carry[] = sprintf('"%s" in table "%s"', $item['fieldName'], $item['tableName']);

                return $carry;
            },
            []
        )
    );
    TCMSLogChange::addInfoMessage(
        sprintf(
            'Handling of translated fields of type "Image with crop" has been fixed, please save these field definitions again in backend to update them: %s',
            $fieldsString
        )
    );
}
