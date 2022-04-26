<h1>Build #1650875276</h1>
<h2>Date: 2022-04-25</h2>
<div class="changelog">
    - Add GA4 based tracker
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('pkg_external_tracker', 'en')
  ->setFields([
      'class_subtype' => '',
      'class_type' => 'Customer',
      'id' => '44922d95-4182-19c6-68c7-9d99ae23d57a',
      'name' => 'Google Analytics GA4',
      'active' => '0',
      'identifier' => '',
      'test_identifier' => '',
      'class' => 'ChameleonSystem\ExternalTrackerGoogleAnalyticsBundle\Bridge\Chameleon\ExternalTracker\ExternalTrackerGoogleAnalyticsGa4',
  ]);
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('pkg_external_tracker', 'de')
  ->setFields([ 'name' => 'Google Universal Analytics (analytics.js, alt)' ])
  ->setWhereEquals([ 'class' => 'ChameleonSystem\ExternalTrackerGoogleAnalyticsBundle\Bridge\Chameleon\ExternalTracker\ExternalTrackerGoogleUniversalAnalytics' ]);
TCMSLogChange::update(__LINE__, $data);

