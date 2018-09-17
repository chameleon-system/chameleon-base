<h1>Build #1536569343</h1>
<h2>Date: 2018-09-10</h2>
<div class="changelog">
    - Add backend messages for password validation
</div>
<?php

TCMSLogChange::AddBackEndMessage('TABLEEDITOR_FIELD_PASSWORD_TOO_SHORT', 'The password in field "[{sFieldTitle:string}]" is too short. Minimum length is [{min}].', '4', 'Password too short.', 'en');
TCMSLogChange::AddBackEndMessage('TABLEEDITOR_FIELD_PASSWORD_TOO_SHORT', 'Das Passwort im Feld "[{sFieldTitle:string}]" ist zu kurz. Bitte mindestens [{min}] Zeichen vergeben.', '4', 'Passwort zu kurz.', 'de');

TCMSLogChange::AddBackEndMessage('TABLEEDITOR_FIELD_PASSWORD_TOO_LONG', 'The password in field "[{sFieldTitle:string}]" is too long. Maximum length is [{max}].', '4', 'Password too long.', 'en');
TCMSLogChange::AddBackEndMessage('TABLEEDITOR_FIELD_PASSWORD_TOO_LONG', 'Das Passwort in Feld "[{sFieldTitle:string}]" ist zu lang. Bitte höchstens [{max}] Zeichen vergeben.', '4', 'Passwort zu lang.', 'de');
