<h1>Build #1746793735</h1>
<h2>Date: 2025-05-09</h2>
<div class="changelog">
    - remove deprecated Universal Analytics tracker
</div>
<?php
$dbConnection = ChameleonSystem\CoreBundle\ServiceLocator::get('database_connection');

$id = $dbConnection
    ->prepare('SELECT id FROM pkg_external_tracker WHERE class = :class')
    ->executeQuery(['class' => 'ChameleonSystem\\ExternalTrackerGoogleAnalyticsBundle\\Bridge\\Chameleon\\ExternalTracker\\ExternalTrackerGoogleUniversalAnalytics'])
    ->fetchOne();

$data = TCMSLogChange::createMigrationQueryData('pkg_external_tracker_cms_portal_mlt', 'de')
    ->setWhereEquals([
        'source_id' => $id,
    ])
;
TCMSLogChange::delete(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('pkg_external_tracker', 'de')
    ->setWhereEquals([
        'id' => $id,
    ])
;
TCMSLogChange::delete(__LINE__, $data);
