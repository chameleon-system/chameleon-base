<h1>Build #1736255433</h1>
<h2>Date: 2025-01-07</h2>
<div class="changelog">
    - ref #65446: add several backend messages when adding selected fields into list/sort fields
</div>
<?php

TCMSLogChange::AddBackEndMessage(
    'FIELDS-INSERTED-INTO-LIST',
    'Successfully inserted the fields into target list.',
    '2', // "Notice"
    'Inserting successful.',
    'en',
);

TCMSLogChange::AddBackEndMessage(
    'FIELDS-INSERTED-INTO-LIST',
    'Erfolgreich die Felder in die Zielliste eingefügt.',
    '2', // "Notice"
    'Einfügen erfolgreich.',
    'de',
);

TCMSLogChange::AddBackEndMessage(
        'FIELD-ALREADY-IN-LIST',
        'Field "[{fieldName:string}]" is already in the target list.',
    '3', // "Warning"
    'Inserting skipped.',
    'en',
);

TCMSLogChange::AddBackEndMessage(
        'FIELD-ALREADY-IN-LIST',
        'Feld "[{fieldName:string}]" is bereits im der Zielliste.',
    '3', // "Warning"
    'Einfügen übersprungen.',
    'de',
);

TCMSLogChange::AddBackEndMessage(
        'ERROR-INSERT-FIELDS-IN-LIST',
        'Selected fields could not inserted into the target list.',
    '4', // "Error"
    'Inserting failed.',
    'en',
);

TCMSLogChange::AddBackEndMessage(
        'ERROR-INSERT-FIELDS-IN-LIST',
        'Die ausgewählten Felder konnten nicht in Zielliste eingefügt werden.',
    '4', // "Error"
    'Einfügen fehlgeschlagen.',
    'de',
);
