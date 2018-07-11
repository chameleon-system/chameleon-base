<h1>update - Build #1493306484</h1>
<h2>Date: 2017-04-27</h2>
<div class="changelog">
</div>
<?php

use Doctrine\Common\Collections\Expr\Comparison;

TCMSLogChange::AddFrontEndMessage('ERROR-EXTRANET-LOGIN-RESET-PASSWORD',
    'Your password was reset because it has not changed for a very long time. Please create a new password using the "Forgot password" function.',
    '4', '', '', 'Core', 'standard', 'en');

TCMSLogChange::AddFrontEndMessage('ERROR-EXTRANET-LOGIN-RESET-PASSWORD',
    'Ihr Passwort wurde zurückgesetzt, da es bereits sehr lange Zeit nicht mehr geändert wurde. Bitte erstellen Sie ein neues Passwort über die "Passwort vergessen"-Funktion',
    '4', '', '', 'Core', 'standard', 'de');

$data = TCMSLogChange::createMigrationQueryData('data_extranet_user', 'en')
    ->setFields(array(
        'password' => '',
    ))
    ->setWhereExpressions(array(
        new Comparison('password', 'LIKE', '_________|%'),
    ))
;
TCMSLogChange::update(__LINE__, $data);
