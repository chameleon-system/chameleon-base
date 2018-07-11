<h1>update - Build #1528274114</h1>
<h2>Date: 2018-06-06</h2>
<div class="changelog">
    - Add snippet chain entry.
</div>
<?php

TCMSLogChange::removeFromSnippetChain('../../../../vendor/chameleon-system/cookie-consent-bundle/Resources/views');
/**
 * Instead of checking if the entry is already present from previous Chameleon versions, we simply remove and re-add.
 */
TCMSLogChange::removeFromSnippetChain('@ChameleonSystemCookieConsentBundle/Resources/views');
TCMSLogChange::addToSnippetChain('@ChameleonSystemCookieConsentBundle/Resources/views', '@ChameleonSystemCoreBundle/Resources/views');
