<h1>Build #1778153180</h1>
<h2>Date: 2026-05-07</h2>
<div class="changelog">
    - #70134: delete all critical file types from cms_filetype table by file_extension
</div>
<?php

$db = TCMSLogChange::getDatabaseConnection();

$criticalExtensions = [
    'php',
    'php3',
    'php4',
    'php5',
    'php7',
    'php8',
    'phtml',
    'phar',
    'inc',
    'module',
    'cgi',
    'pl',
    'py',
    'rb',
    'sh',
    'bash',
    'zsh',
    'java',
    'class',
    'jar',
    'jsp',
    'js',
    'mjs',
    'cjs',
    'html',
    'htm',
    'xhtml',
    'xml',
    'swf',
];

$quotedExtensions = array_map(static fn (string $extension): string => $db->quote($extension), $criticalExtensions);
$deletionQuery = 'DELETE FROM `cms_filetype` WHERE LOWER(`file_extension`) IN ('.implode(', ', $quotedExtensions).')';

$db->query($deletionQuery);
