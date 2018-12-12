<h1>Build #1544598819</h1>
<h2>Date: 2018-12-12</h2>
<div class="changelog">
    - #237: Add missing translation field
</div>
<?php

TCMSLogChange::RunQuery(__LINE__, "ALTER TABLE `pkg_cms_credit_check` 
ADD COLUMN `text_credit_check_pending__de` LONGTEXT NOT NULL AFTER `text_credit_check_pending`");

//  COMMENT 'Hinweistext: Text der angezeigt wird wenn eine Bonitätsüberprüfung durchgeführt wird.\n\nEs stehen folgende Parameter zur Verfügung:\n\n[{firstName}] - Vorname\n[{lastName}] - Nachname\n[{company}] - Firma\n[{street}] - Strasse + Hausnummer\n[{streetName}] -'
