<h1>Build #1550235650</h1>
<h2>Date: 2019-02-15</h2>
<div class="changelog">
    - #275: Add info message for hreflang
</div>
<?php

TCMSLogChange::addInfoMessage('hreflang: MTPageMeta provides now the array "language-alternatives" with URLs to the current page in other languages.
If you want to support that in your theme see chameleon-shop-theme-bundle/.../MTPageMeta/standard.view.php for an example usage.', TCMSLogChange::INFO_MESSAGE_LEVEL_TODO);
