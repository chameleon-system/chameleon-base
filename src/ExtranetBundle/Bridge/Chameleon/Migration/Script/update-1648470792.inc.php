<h1>Build #1648470792</h1>
<h2>Date: 2022-03-28</h2>
<div class="changelog">
    - ref #748: Add a (success) message in case of a "double opt-in" registration.
</div>
<?php

use ChameleonSystem\ExtranetBundle\MessageCodes;

TCMSLogChange::AddFrontEndMessage(
    MessageCodes::WAIT_FOR_EMAIL_CONFIRM,
    'A confirmation email has be sent to your address. Please confirm it by clicking on the link there.',
    TCMSLogChange::getMessageTypeIdBySystemName('notice'),
    '', '', 'Core', 'standard',
    'en'
);

$data = TCMSLogChange::createMigrationQueryData('cms_message_manager_message', 'de')
    ->setFields([
        'message' => 'Es wurde eine E-Mail an Ihre Adresse geschickt. Bitte bestätigen Sie Ihre Anmeldung durch einen Klick auf den Link dort.',
    ])
    ->setWhereEquals([
        'name' => MessageCodes::WAIT_FOR_EMAIL_CONFIRM,
    ])
;
TCMSLogChange::update(__LINE__, $data);
