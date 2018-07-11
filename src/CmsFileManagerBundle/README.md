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

The following methods are available:

* put($sSource, $sTarget, $chmod=null, $remoteOnly=false);
* move($source, $target);
* copy($source, $target);
* get($sPath);
* delete($file);
* unlink($file);
* symlink($target, $link);
* setDriver(IClusterDriver $driver);
* chmod($file, $mode);
* mkdir($path, $mode = 0777, $recursive = false);
* rmdir($path);
* deldir($path, $recursive = false);
* fopen($filename, $mode, $use_include_path = null);
* fwrite($handle, $string, $length = null);
* fclose($handle);
* file_put_contents($filename, $data);
