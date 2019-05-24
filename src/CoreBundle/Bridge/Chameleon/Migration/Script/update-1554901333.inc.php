<h1>Build #1551195244</h1>
<h2>Date: 2019-02-26</h2>
<div class="changelog">
</div>
<?php

TCMSLogChange::addInfoMessage('Pages are now only available if they are part of a navigation (see navigation
options in the portal settings). This restores the behavior of a previous Chameleon release; it was never intended that
every page is accessible in the frontend. Ensure that all pages that should be available in the frontend are part of a
navigation.', TCMSLogChange::INFO_MESSAGE_LEVEL_TODO);
