<h1>Build #1785160023</h1>
<h2>Date: 2026-07-27</h2>
<div class="changelog">
    - #70643: undo table changes that where introduced in an MR. The original update was removed but since some installations may already have the update, we need to rollback the changes
</div>
<?php
if (TCMSLogChange::FieldExists('cms_portal_domains', 'url_suffix')) {

    TCMSLogChange::deleteField('cms_portal_domains', 'url_suffix');

    $query = 'ALTER TABLE cms_portal_domains
    DROP INDEX uniq_portal_url_suffix,
    DROP INDEX idx_portal_name_url_suffix,
    DROP INDEX idx_portal_sslname_url_suffix,
    DROP COLUMN url_suffix_unique,
    DROP COLUMN url_suffix;';
    TCMSLogChange::RunQuery(__LINE__, $query);
}

TCMSLogChange::deleteBackEndMessage('TABLEEDITOR_DOMAIN_URL_SUFFIX_REQUIRES_LANGUAGE');
TCMSLogChange::deleteBackEndMessage('TABLEEDITOR_DOMAIN_URL_SUFFIX_NOT_UNIQUE');
TCMSLogChange::deleteBackEndMessage('TABLEEDITOR_DOMAIN_URL_SUFFIX_PORTAL_IDENTIFIER_CONFLICT');