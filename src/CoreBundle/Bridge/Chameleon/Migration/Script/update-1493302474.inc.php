<h1>update - Build #1493302474</h1>
<h2>Date: 2017-04-27</h2>
<div class="changelog">
</div>
<?php

use Doctrine\Common\Collections\Expr\Comparison;

$data = TCMSLogChange::createMigrationQueryData('cms_user', 'en')
    ->setFields(array(
        'crypted_pw' => '',
    ))
    ->setWhereExpressions(array(
        new Comparison('crypted_pw', 'LIKE', '_________|%'),
    ))
;
TCMSLogChange::update(__LINE__, $data);
