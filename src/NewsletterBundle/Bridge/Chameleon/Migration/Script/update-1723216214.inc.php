<h1>Build #1723216214</h1>
<h2>Date: 2024-08-09</h2>
<div class="changelog">
    - ref #64304: set table editor class for newsletter group configuration
</div>
<?php

$data = TCMSLogChange::createMigrationQueryData('cms_tbl_conf', 'de')
    ->setFields([
//      'translation' => 'Newsletter-Empfängerlisten',
        'table_editor_class' => 'ChameleonSystem\NewsletterBundle\Bridge\Chameleon\TableEditor\TableEditorNewsletterGroup',
    ])
    ->setWhereEquals([
        'name' => 'pkg_newsletter_group',
    ])
;
TCMSLogChange::update(__LINE__, $data);
