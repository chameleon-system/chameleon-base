<h1>update - Build #1527525464</h1>
<h2>Date: 2018-05-28</h2>
<div class="changelog">
    add error message backend<br/>
</div>
<?php
TCMSLogChange::AddBackEndMessage(
    'IMAGE-CROP-SAVE-FAILED',
    'Error saving cutout.',
    '4',
    TCMSMessageManager::AUTO_CREATED_MARKER,
    'en'
);
TCMSLogChange::AddBackEndMessage(
    'IMAGE-CROP-SAVE-FAILED',
    'Ausschnitt konnte nicht gespeichert werden.',
    '4',
    'Ausschnitt konnte nicht gespeichert werden.',
    'de'
);
