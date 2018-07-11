<h1>update - Build #1478679465</h1>
<h2>Date: 2016-11-09</h2>
<div class="changelog">
    - #36048: Clear less dir once
</div>
<?php

function delTree($dir)
{
    // Delete all contents recursively; but not the directory/ies itself

    $files = array_diff(scandir($dir), array('.', '..'));
    foreach ($files as $file) {
        is_dir("$dir/$file") ? delTree("$dir/$file") : unlink("$dir/$file");
    }
}

    $lessCompiler = new \ChameleonSystem\ViewRendererBundle\objects\TPkgViewRendererLessCompiler();
    $cachePath = $lessCompiler->getLocalPathToCompiledLess();

    delTree($cachePath);
