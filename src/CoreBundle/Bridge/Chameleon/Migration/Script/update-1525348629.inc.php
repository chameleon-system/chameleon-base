<h1>Build #1525348629</h1>
<h2>Date: 2018-05-03</h2>
<div class="changelog">
    - Hide deprecated fields class_type and class_subtype
</div>
<?php

use Doctrine\Common\Collections\Expr\Comparison;

$comparison = new Comparison('name', Comparison::IN, [
    'class_type',
    'class_subtype',
    'class_sub_type',
    'exit_class_type',
    'exit_class_subtype',
    'fieldclass_subtype',
    'pkg_comment_class_type',
    'pkg_comment_class_sub_type',
    'table_editor_class_type',
    'table_editor_class_subtype',
]);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'en')
    ->setFields(array(
        'modifier' => 'hidden',
        '049_helptext' => '@deprecated since 6.2.0 - field is no longer used, but preserved to ensure backwards compatibility',
    ))
    ->setWhereExpressions([$comparison])
;
TCMSLogChange::update(__LINE__, $data);

$data = TCMSLogChange::createMigrationQueryData('cms_field_conf', 'de')
    ->setFields(array(
        '049_helptext' => '@deprecated since 6.2.0 - field is no longer used, but preserved to ensure backwards compatibility',
    ))
    ->setWhereExpressions([$comparison])
;
TCMSLogChange::update(__LINE__, $data);
