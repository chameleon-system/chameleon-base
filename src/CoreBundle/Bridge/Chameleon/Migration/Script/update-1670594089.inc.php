<h1>Build #1670594089</h1>
<h2>Date: 2022-12-09</h2>
<div class="changelog">
    - upgrade symfony
</div>
<?php

$connection = TCMSLogChange::getDatabaseConnection();

// https://github.com/symfony/symfony/blob/5.4/UPGRADE-5.0.md#httpfoundation
$connection->executeQuery("ALTER TABLE `_cms_sessions` MODIFY `sess_lifetime` INTEGER UNSIGNED NOT NULL");
