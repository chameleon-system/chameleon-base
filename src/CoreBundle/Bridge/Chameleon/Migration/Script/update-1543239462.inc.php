<h1>Build #1543239462</h1>
<h2>Date: 2018-11-26</h2>
<div class="changelog">
</div>
<?php

TCMSLogChange::addInfoMessage('It is important to make sure that calls to TCMSRecordList::ChangeOrderBy() and
\ChameleonSystem\core\DatabaseAccessLayer\QueryModifierOrderByInterface::getQueryWithOrderBy() use properly escaped 
field name arguments if input comes from users. Not doing so imposes a severe security risk.', TCMSLogChange::INFO_MESSAGE_LEVEL_TODO);
