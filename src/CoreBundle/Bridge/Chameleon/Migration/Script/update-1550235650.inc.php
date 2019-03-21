<h1>Build #1550235650</h1>
<h2>Date: 2019-02-15</h2>
<div class="changelog">
    - #275: Add info message for hreflang
</div>
<?php

TCMSLogChange::addInfoMessage('MTPageMeta now provides an additional variable "language-alternatives" that allows to add URLs to alternative languages of the same page using hreflang meta tags.
 See the view MTPageMeta/standard.view.php in chameleon-shop-theme-bundle for an example.', TCMSLogChange::INFO_MESSAGE_LEVEL_INFO);
