<h1>Build #1586876526</h1>
<h2>Date: 2020-04-14</h2>
<div class="changelog">
    - #575: Show information message about login names
</div>
<?php

TCMSLogChange::addInfoMessage(
    'Consider setting PKG_EXTRANET_USE_CASE_INSENSITIVE_LOGIN_NAMES to true. This was just done in the demo shop so is valid for any new project.',
    TCMSLogChange::INFO_MESSAGE_LEVEL_WARNING
);
