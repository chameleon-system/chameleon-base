<h1>Build #1554106544</h1>
<h2>Date: 2019-04-01</h2>
<div class="changelog">
    - Remove CMS search indexer module
</div>
<?php


$data = TCMSLogChange::createMigrationQueryData('cms_module', 'en')
    ->setWhereEquals([
            'uniquecmsname' => 'cmssearchindexer',
    ]);
TCMSLogChange::delete(__LINE__, $data);
