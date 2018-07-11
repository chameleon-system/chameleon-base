<h1>update - Build #1517487287</h1>
<h2>Date: 2018-02-01</h2>
<div class="changelog">
</div>
<?php

$etrackerFieldId = TCMSLogChange::GetTableFieldId(TCMSLogChange::GetTableId('cms_portal'), 'etracker_id');

  $data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
      ->setFields(array(
          '049_helptext' => 'Enter your etracker ID to enable tracking for this portal. http://www.etracker.de

@deprecated since 6.2.0 - use pkgExternalTrackerEtracker instead.',
      ))
      ->setWhereEquals(array(
          'id' => $etrackerFieldId,
      ))
  ;
  TCMSLogChange::update(__LINE__, $data);

  $data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
      ->setFields(array(
          'modifier' => 'hidden',
          '049_helptext' => 'Geben Sie hier Ihre etracker-ID ein, um das Tracking für dieses Portal zu aktivieren. http://www.etracker.de

@deprecated since 6.2.0 - use pkgExternalTrackerEtracker instead.',
      ))
      ->setWhereEquals(array(
          'id' => $etrackerFieldId,
      ))
  ;
  TCMSLogChange::update(__LINE__, $data);
