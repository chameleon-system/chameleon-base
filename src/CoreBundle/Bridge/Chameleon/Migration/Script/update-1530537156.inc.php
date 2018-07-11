<h1>Build #1530537156</h1>
<h2>Date: 2018-07-02</h2>
<div class="changelog">
    - Set ID for flawed message manager messages.
</div>
<?php

$query = "SELECT `name` FROM `cms_message_manager_message` WHERE `id` = ''";

$databaseConnection = TCMSLogChange::getDatabaseConnection();
$statement = $databaseConnection->executeQuery($query);
while ($name = $statement->fetch(\PDO::FETCH_COLUMN)) {
    $data = TCMSLogChange::createMigrationQueryData('cms_message_manager_message', 'en')
      ->setFields([
          'id' => TCMSLogChange::createUnusedRecordId('cms_message_manager_message'),
      ])
      ->setWhereEquals([
          'id' => '',
          'name' => $name,
      ])
    ;
    TCMSLogChange::update(__LINE__, $data);
}


