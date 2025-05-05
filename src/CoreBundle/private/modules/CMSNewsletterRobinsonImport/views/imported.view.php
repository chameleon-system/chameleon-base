<?php
?>
<br/>
<h1><?php echo ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_newsletter_robinson_import.headline'); ?></h1>
<h2><?php echo ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_newsletter_robinson_import.result'); ?> (<?php echo count($data['aListImported']); ?>)</h2>
<?php
$count = 0;
foreach ($data['aListImported'] as $email) {
    ++$count;
    echo $count.'. '.$email."<br />\n";
}
?>
<h2><?php echo ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.cms_module_newsletter_robinson_import.import_error'); ?> (<?php echo count($data['aListIgnored']); ?>)</h2>
<?php
$count = 0;
foreach ($data['aListIgnored'] as $email) {
    ++$count;
    echo $count.'. '.$email."<br />\n";
}
