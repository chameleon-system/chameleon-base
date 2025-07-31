<h1>Build #1753965995</h1>
<h2>Date: 2025-07-31</h2>
<div class="changelog">
    - ref #61998: add markdown bundle to snippet chain
</div>
<?php

TCMSLogChange::addToSnippetChain(
    '@ChameleonSystemMarkdownCmsBundle/Resources/views',
    null,
    ['5f047d9b-0c20-0bfb-2dce-f8193653965c'] // `pkg_cms_theme`.id for "backend"
);
