<h1>Build #1766480504</h1>
<h2>Date: 2025-12-23</h2>
<div class="changelog">
    - ref #69054: add backend messages for validation of configured MLT tables
</div>
<?php

TCMSLogChange::AddBackEndMessage(\TCMSFieldLookupMultiselect::TABLEEDITOR_TABLE_NAME_MUST_END_WITH_MLT_MESSAGE_NAME, 'Der Tabellenname "[{tableName}]" in der muss auf "_mlt" enden.');
TCMSLogChange::AddBackEndMessage(\TCMSFieldLookupMultiselect::TABLEEDITOR_TABLE_NAME_TOO_LONG_MESSAGE_NAME, 'Der Tabellenname "[{tableName}]" hat [{tableNameLength}] Zeichen, darf aber nur max. 64 Zeichen haben.');
TCMSLogChange::AddBackEndMessage(\TCMSFieldLookupMultiselect::TABLE_ALREADY_EXISTS_SHORT_MESSAGE_NAME, 'Eine Tabelle mit dem SQL-Namen "[{tableName}]" existiert bereits.');
