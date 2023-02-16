<h1>Build #1676561982</h1>
<h2>Date: 2023-03-15</h2>
<div class="changelog">
    - delete unused field
</div>
<?php
TCMSLogChange::deleteField('cms_user', 'cms_workflow_transaction_id');