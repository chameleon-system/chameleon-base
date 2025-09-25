<h1>Build #1742210912</h1>
<h2>Date: 2025-03-17</h2>
<div class="changelog">
    - require shop bundle updates and update autoclasses to prevent exception while calling autoclass in update #1742210913
</div>
<?php

TCMSLogChange::requireBundleUpdates('ChameleonSystemShopBundle', 1734592869);
TCMSLogChange::UpdateAutoClasses();