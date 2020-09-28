<h1>pkgnewsletter - Build #1601284542</h1>
<div class="changelog">
    - add message for "newsletter is not found"
</div>
<?php

TCMSLogChange::AddFrontEndMessage(
    'ERROR-UNSUBSCRIBE-NEWSLETTER-USER-NOT-FOUND',
    'This newsletter subscription could not be found in our mailing list.',
    '4',
    'Given newsletter user ID or email address was not found when trying to unsubscribe.',
    '',
    'Core',
    'standard',
    'en'
);

TCMSLogChange::AddFrontEndMessage(
    'ERROR-UNSUBSCRIBE-NEWSLETTER-USER-NOT-FOUND',
    'Diese Newsletter-Anmeldung wurde in unserem Verteiler nicht gefunden.',
    '4',
    'Vom Newsletter abmelden: Die gegebene Newsletter-User ID oder E-Mail-Adresse wurde nicht gefunden.',
    '',
    'Core',
    'standard',
    'de'
);
