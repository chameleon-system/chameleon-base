<h1>Build #1732093885</h1>
<h2>Date: 2024-11-20</h2>
<div class="changelog">
    - ref #64995: deprecate callback activation field for list view items <br>
    - display potentially configurations conflicts
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
  ->setFields([
      //'name' => 'use_callback',
      //'translation' => 'Callbackfunktion aktivieren',
      'modifier' => 'hidden',
      '049_helptext' => '@deprecated #64995',
  ])
  ->setWhereEquals([
      'id' => '998',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$query ="ALTER TABLE `cms_tbl_display_list_fields`
                     CHANGE `use_callback`
                            `use_callback` ENUM('0','1') DEFAULT '0' NOT NULL COMMENT 'Callbackfunktion aktivieren: @deprecated #64995'";
TCMSLogChange::RunQuery(__LINE__, $query);

// display potentially configurations conflicts
$query = "
SELECT `fields`.`name`, `use_callback`, `callback_fnc`, `cms_tbl_conf`.`name` AS `table_name`
    FROM `cms_tbl_display_list_fields` `fields`
    JOIN `cms_tbl_conf` ON `cms_tbl_conf`.`id` = `fields`.`cms_tbl_conf_id`
    WHERE (`use_callback` = '1') != (Trim(`callback_fnc`) != '')
";

$connection = TCMSLogChange::getDatabaseConnection();
$conflicts = $connection->fetchAllAssociative($query);

if ([] !== $conflicts) {
    TCMSLogChange::addInfoMessage("The field \"Callbackfunktion aktivieren\" (`cms_tbl_display_list_fields`.`use_callback`) is deprecated and hidden now.\n"
        ."Take a look at following \"Listenfelder\" fields of several tables, you may deactivate the callback function by resetting this field and emptying the corresponding function name.\n"
        , "These fields have a defined callback function, but are deactivated, or vice versa.", TCMSLogChange::INFO_MESSAGE_LEVEL_WARNING);

    foreach ($conflicts as $conflict) {
        TCMSLogChange::addInfoMessage(sprintf("Table '%s' field '%s': callback_fnc: '%s', use_callback: '%s'.\n", $conflict['table_name'], $conflict['name'], $conflict['callback_fnc'], $conflict['use_callback']));
    }
}
