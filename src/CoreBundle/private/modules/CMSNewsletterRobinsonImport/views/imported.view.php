<?php
?>
<br/>
<h1><?=TGlobal::Translate('chameleon_system_core.cms_module_newsletter_robinson_import.headline'); ?></h1>
<h2><?=TGlobal::Translate('chameleon_system_core.cms_module_newsletter_robinson_import.result'); ?> (<?=count($data['aListImported']); ?>)</h2>
<?php
$count = 0;
foreach ($data['aListImported'] as $email) {
    ++$count;
    echo $count.'. '.$email."<br />\n";
}
?>
<h2><?=TGlobal::Translate('chameleon_system_core.cms_module_newsletter_robinson_import.import_error'); ?> (<?=count($data['aListIgnored']); ?>)</h2>
<?php
$count = 0;
foreach ($data['aListIgnored'] as $email) {
    ++$count;
    echo $count.'. '.$email."<br />\n";
}
