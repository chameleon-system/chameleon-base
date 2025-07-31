<h1>Build #1753965916</h1>
<h2>Date: 2025-07-31</h2>
<div class="changelog">
    - ref #61998: add extension to document table to build the full filename
</div>
<?php

if (false === class_exists('ChameleonSystemMarkdownCmsBundleBridgeChameleonEntityDocumentFileDownloadAutoParent')) {
    TCMSLogChange::AddExtensionAutoParentToTable(
        'cms_document',
        'ChameleonSystem\MarkdownCmsBundle\Bridge\Chameleon\Entity\DocumentFileDownload'
    );
}
