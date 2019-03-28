Chameleon System CmsFileManagerBundle
=====================================

This interface handles file operations.
You should use it anywhere except for temporary files.
The file manager is accessible through a service container
most of the API calls are named like the corresponding PHP methods

## Usage:

### Basic example
```
<?php
/** @var IPkgCmsFileManager $fileManager */
$fileManager = \ChameleonSystem\CoreBundle\ServiceLocator::get("chameleon_system_core.filemanager");
$fileManager->mkdir($targetDir, 0777, true);
?>
```

### Writing a file
```
<?php
/** @var IPkgCmsFileManager $fileManager */
$fileManager = \ChameleonSystem\CoreBundle\ServiceLocator::get("chameleon_system_core.filemanager");

$newFile = $filemanager->fopen($tmpFileName, "wb");
$filemanager->fwrite($newFile, $fileContent);
$filemanager->fclose($newFile);
?>
```

See Interface `IPkgCmsFileManager` for available methods.
