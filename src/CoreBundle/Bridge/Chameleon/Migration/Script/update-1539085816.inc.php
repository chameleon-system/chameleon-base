<h1>Build #1539085816</h1>
<h2>Date: 2018-10-09</h2>
<div class="changelog">
    - #118: Add error message for maintenance mode problems
</div>
<?php

TCMSLogChange::AddFrontEndMessage(
    'TABLEEDITOR_CMS_CONFIG_MAINTENANCE_ERROR',
    "Einstellung für 'Alle Webseiten abschalten' konnte nicht gespeichert werden: [{exceptionMessage}]",
    TCMSLogChange::GetMessageTypeByName('Fehler'),
    '', '', 'Core', 'standard', 'de'
);

TCMSLogChange::AddFrontEndMessage(
    'TABLEEDITOR_CMS_CONFIG_MAINTENANCE_ERROR',
    "Setting for 'Turn off all websites' could not be saved: [{exceptionMessage}]",
    TCMSLogChange::GetMessageTypeByName('Fehler'),
    '', '', 'Core', 'standard', 'en'
);


