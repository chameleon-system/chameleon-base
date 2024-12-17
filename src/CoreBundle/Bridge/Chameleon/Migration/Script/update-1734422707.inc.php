<h1>Build #1734422707</h1>
<h2>Date: 2024-12-17</h2>
<div class="changelog">
    - #65295: sort fields of cronjobs
</div>
<?php

$cronjobTableId = TCMSLogChange::GetTableId('cms_cronjobs');

TCMSLogChange::SetFieldPosition($cronjobTableId, 'name', 'id');
TCMSLogChange::SetFieldPosition($cronjobTableId, 'cron_class', 'name');
TCMSLogChange::SetFieldPosition($cronjobTableId, 'active', 'cron_class');
TCMSLogChange::SetFieldPosition($cronjobTableId, 'start_execution', 'active');
TCMSLogChange::SetFieldPosition($cronjobTableId, 'lock', 'start_execution');
TCMSLogChange::SetFieldPosition($cronjobTableId, 'last_execution', 'lock');
TCMSLogChange::SetFieldPosition($cronjobTableId, 'real_last_execution', 'last_execution');
TCMSLogChange::SetFieldPosition($cronjobTableId, 'end_execution', 'real_last_execution');
TCMSLogChange::SetFieldPosition($cronjobTableId, 'execute_every_n_minutes', 'end_execution');
TCMSLogChange::SetFieldPosition($cronjobTableId, 'unlock_after_n_minutes', 'execute_every_n_minutes');

