<h1>Build #1601044140</h1>
<h2>Date: 2020-09-25</h2>
<div class="changelog">
    - Info about "Forgot Password" email.
</div>
<?php
TCMSLogChange::addInfoMessage(
    'The "Forgot Password" email was changed to not include the user\'s email address 
anymore. If \TdbDataExtranet::GetPasswordChangeURL() is overwritten in custom code, consider removing the email from the 
URL. Also, any custom code that relies on the email being present in the URL, needs to be changed. This would most likely 
be MTExtranet::ChangeForgotPassword() in case it is overwritten/extended.',
    TCMSLogChange::INFO_MESSAGE_LEVEL_INFO
);
