<h1>Build #1708346253</h1>
<h2>Date: 2024-02-19</h2>
<div class="changelog">
    - ref #843: show info/to-do message if folders in "src/extensions/objectview" could be moved into a theme folder
</div>
<?php

use ChameleonSystem\CoreBundle\ServiceLocator;

$connection = TCMSLogChange::getDatabaseConnection();

$kernel = ServiceLocator::get('kernel');
$projectPath = $kernel->getProjectDir();

$extensionsPath = $projectPath.'/src/extensions/objectviews';
if (false === file_exists($extensionsPath) || false === is_dir($extensionsPath)) {
    return;
}

$dirs = array_diff(scandir($extensionsPath), ['.', '..']);
if ([] === $dirs) {
    return;
}

$backendThemeName = $connection->fetchOne('SELECT `pkg_cms_theme`.`name` FROM  `pkg_cms_theme` JOIN `cms_config` ON `pkg_cms_theme`.`id` = `cms_config`.`pkg_cms_theme_id`');
$backendThemeName = $backendThemeName ?: '?';

$themePaths = [];
foreach ($kernel->getBundles() as $bundle) {
    if (false === str_ends_with($bundle->getName(), 'ThemeBundle') || $bundle->getNamespace() === 'ChameleonSystem\ChameleonShopThemeBundle') {
        continue;
    }

    $path = $bundle->getPath();
    if (true === str_starts_with($path, $projectPath)) {
        $path = '.'.substr($path, strlen($projectPath));
    }

    $themePaths[$bundle->getName()] = $path;
}

$output = 'Move your ./src/extensions/objectviews/{'.implode(',', $dirs).'}';
$output .= ' into one of your preferred themes\' folder and add a snippet path to the backend\'s ("'.$backendThemeName.'") snippet-chain eventually:';
foreach ($themePaths as $name => $path) {
    $output .= "\n".$name.' --> '.$path.'/Resources/views/objectviews';
}

$output .= "\nNote: keep an eye on symlinks if any exist, they should be 'refreshed'";

TCMSLogChange::addInfoMessage($output, TCMSLogChange::INFO_MESSAGE_LEVEL_TODO);
