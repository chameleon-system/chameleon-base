<h1>Build #1740496732</h1>
<h2>Date: 2025-02-25</h2>
<div class="changelog">
    - ref #65906: add joined index for cms_portal_id and data_country_id in data_extranet_user
</div>
<?php
$query = 'ALTER TABLE `data_extranet_user`
            ADD INDEX `cms_portal_id_data_country_id` (`cms_portal_id`,`data_country_id`)';
TCMSLogChange::RunQuery(__LINE__, $query);

