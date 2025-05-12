<h1>update - Build #1490706399</h1>
<h2>Date: 2017-03-28</h2>
<div class="changelog">
    #38232<br/>
    set changed date<br>
</div>
<?php
TCMSLogChange::requireBundleUpdates('ChameleonSystemCoreBundle', 1490106136);

$databaseConnection = TCMSLogChange::getDatabaseConnection();
$rows = $databaseConnection->fetchAllAssociative('SELECT `id`, `time_stamp` FROM `cms_media`');
foreach ($rows as $row) {
    $databaseConnection->update(
        'cms_media',
        ['date_changed' => date('Y-m-d H:i:s', strtotime($row['time_stamp']))],
        ['id' => $row['id']]
    );
}
