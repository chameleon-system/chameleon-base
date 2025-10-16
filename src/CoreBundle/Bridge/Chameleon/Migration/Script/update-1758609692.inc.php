<h1>update - Build #1758609692</h1>
<h2>Date: 2025-09-23</h2>
<div class="changelog">
    - #67927: drop index from metatags field in cms_media table
</div>
<?php

$query = "ALTER TABLE cms_media DROP INDEX metatags";

TCMSLogChange::RunQuery(__LINE__, $query);