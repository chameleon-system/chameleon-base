<h1>Build #1637069390</h1>
<div class="changelog">
    - ref #736: add info about template change
</div>
<?php

TCMSLogChange::addInfoMessage('The text block rendering now passes all callTimeVars to the wysiwyg rendering.
If your theme has a copy of: pkgCmsTextBlock/views/db/TPkgCmsTextBlock/standard.view.php, 
you should add the fourth parameter: echo $oTextBlock->GetTextField("content", $iWidth, false, $placeholders);');
