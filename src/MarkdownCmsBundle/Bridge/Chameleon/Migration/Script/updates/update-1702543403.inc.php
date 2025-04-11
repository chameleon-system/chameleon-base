<h1>Build #1702543403</h1>
<h2>Date: 2023-12-21</h2>
<div class="changelog">
    - ref #61998: add markdown bundle to snippet chain
</div>
<?php
// check if fieldtype already exists
$fieldTypeId = TCMSLogChange::GetFieldType('CMSFIELD_MARKDOWNTEXT', false);

if ('' === $fieldTypeId) {
    TCMSLogChange::addToSnippetChain(
        '@ChameleonSystemMarkdownCmsBundle/Resources/views',
        null,
        ['5f047d9b-0c20-0bfb-2dce-f8193653965c']
    );
}
