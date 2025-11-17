<h1>Build #1763370967</h1>
<h2>Date: 2025-11-17</h2>
<div class="changelog">
    - #68187: add message for failed field validation
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_message_manager_backend_message', 'de')
  ->setFields([
      'name' => '',
      'id' => '342b06d9-fb41-577d-83f4-6e5d53d17042',
  ])
;
TCMSLogChange::insert(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_message_manager_backend_message', 'de')
  ->setFields([
      'name' => 'TABLEEDITOR_SAVE_FIELD_VALIDATION_ERROR', // prev.: ''
      'cms_message_manager_message_type_id' => '4', // prev.: ''
      'cms_config_id' => '1',
      'message' => 'Die Feld-Validierung war nciht erfolgreich. Bitte überprüfen Sie Ihre Angaben', // prev.: ''
  ])
  ->setWhereEquals([
      'id' => '342b06d9-fb41-577d-83f4-6e5d53d17042',
  ])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_message_manager_backend_message', 'en')
    ->setFields([
        'message' => 'The validation of the input fields was not successfull. Please check your input', // prev.: ''
    ])
    ->setWhereEquals([
        'id' => '342b06d9-fb41-577d-83f4-6e5d53d17042',
    ])
;
TCMSLogChange::update(__LINE__, $data);