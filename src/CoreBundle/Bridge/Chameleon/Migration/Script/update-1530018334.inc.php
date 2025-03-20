<h1>Build #1530018334</h1>
<h2>Date: 2018-06-26</h2>
<div class="changelog">
    - Apply new default image
</div>
<?php

$databaseConnection = TCMSLogChange::getDatabaseConnection();
$filesize = (int) $databaseConnection->fetchOne("SELECT `filesize` FROM `cms_media` WHERE `id` = '1'");

// Do not replace image if customized.
if (782 !== $filesize) {
    return;
}

$imageData = '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';

$subPath = $databaseConnection->fetchOne("SELECT `path` FROM `cms_media` WHERE `id` = '1'");
$path = PATH_MEDIA_LIBRARY.$subPath;

\file_put_contents($path, \base64_decode($imageData));

$data = TCMSLogChange::createMigrationQueryData('cms_media', 'en')
    ->setFields([
        'filesize' => \filesize($path),
        'height' => 256,
        'width' => 256,
    ])
    ->setWhereEquals([
        'id' => '1',
    ])
;
TCMSLogChange::update(__LINE__, $data);

if (false === is_dir(PATH_MEDIA_LIBRARY_THUMBS.'c/4c/')) {
    return;
}

$finder = \Symfony\Component\Finder\Finder::create()
    ->files()
    ->in(PATH_MEDIA_LIBRARY_THUMBS.'c/4c/')
;
foreach ($finder->getIterator() as $file) {
    \unlink($file->getRealPath());
}
