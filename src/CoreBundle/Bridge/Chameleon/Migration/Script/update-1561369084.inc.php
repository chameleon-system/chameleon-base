<h1>Build #1561369084</h1>
<h2>Date: 2019-06-24</h2>
<div class="changelog">
    - #427: Check for interfering first portal
</div>
<?php

$connection = TCMSLogChange::getDatabaseConnection();

$firstPortalId = $connection->fetchColumn('SELECT `id` FROM `cms_portal` ORDER BY `id` ASC LIMIT 1');

$primaryPortalId = $connection->fetchColumn('SELECT `cms_portal_id` FROM `cms_config`');

if ($firstPortalId !== $primaryPortalId) {
    TCMSLogChange::addInfoMessage(
        sprintf(
            'Your default portal changed (result of PortalDomainService::getDefaultPortal()): It was the one with id %s and is now the one configured as primary %s.',
            $firstPortalId,
            $primaryPortalId
        ),
        TCMSLogChange::INFO_MESSAGE_LEVEL_WARNING
    );
}
