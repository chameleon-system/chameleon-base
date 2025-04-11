<h1>Build #1705324190</h1>
<h2>Date: 2024-01-15</h2>
<div class="changelog">
    - ref #61998: add extension to document table to build the full filename
</div>
<?php
// check if fieldtype already exists
$fieldTypeId = TCMSLogChange::GetFieldType('CMSFIELD_MARKDOWNTEXT', false);

if ('' === $fieldTypeId) {
    TCMSLogChange::AddExtensionAutoParentToTable(
        'cms_document',
        'ChameleonSystem\MarkdownCmsBundle\Bridge\Chameleon\Entity\DocumentFileDownload'
    );
}
