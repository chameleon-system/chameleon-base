<?php
?>
<br/>
<h1><?php echo ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_newsletter_import.headline'); ?></h1>

<h2><?php echo ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_newsletter_import.import_invalid_data'); ?> (<?php echo count($data['aListErrorImport']); ?>)</h2>
<?php
$count = 0;
foreach ($data['aListErrorImport'] as $email) {
    ++$count;
    echo $count.'. '.$email."<br />\n";
}
?>
<h2><?php echo ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_newsletter_import.import_updated_invalid_data'); ?> (<?php echo count($data['aListErrorUpdate']); ?>)</h2>
<?php
$count = 0;
foreach ($data['aListErrorUpdate'] as $email) {
    ++$count;
    echo $count.'. '.$email."<br />\n";
}
?>
<h2><?php echo ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_newsletter.import_success'); ?> (<?php echo count($data['aListImported']); ?>)</h2>
<?php
$count = 0;
foreach ($data['aListImported'] as $email) {
    ++$count;
    echo $count.'. '.$email."<br />\n";
}
?>
<h2><?php echo ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_newsletter_import.import_updated'); ?> (<?php echo count($data['aListUpdated']); ?>)</h2>
<?php
$count = 0;
foreach ($data['aListUpdated'] as $email) {
    ++$count;
    echo $count.'. '.$email."<br />\n";
}

?>
<h2><?php echo ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_newsletter_import.import_error'); ?> (<?php echo count($data['aListIgnored']); ?>)</h2>
<?php
$count = 0;
foreach ($data['aListIgnored'] as $email) {
    ++$count;
    echo $count.'. '.$email."<br />\n";
}
