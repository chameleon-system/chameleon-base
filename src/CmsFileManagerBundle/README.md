Chameleon System CmsFileManagerBundle
=====================================

Overview
--------
The CmsFileManagerBundle provides a simple wrapper around Symfony's Filesystem component for
common file and directory operations within the Chameleon System. It offers methods to copy,
move, delete files and directories, create symlinks, adjust permissions, and perform stream-based writes.

Note: This bundle is deprecated as of Chameleon System 8.0.0. Please prefer Symfony's Filesystem directly.

Features
--------
- File operations: `put`, `move`, `copy`, `delete`, `unlink`
- Directory operations: `mkdir`, `rmdir`, `deldir`
- Create symbolic links: `symlink`
- Change file permissions: `chmod`
- Stream-based writes: `fopen`, `fwrite`, `fclose`, `file_put_contents`
- Atomic `put` (rename + optional chmod)

Service
-----------------------

Use this service (`chameleon_system_cms_file_manager.file_manager`) to perform file and directory operations.

Usage Examples
--------------
Inject the `IPkgCmsFileManager` service into your class:
```php
use IPkgCmsFileManager;

class FileService
{
    public function __construct(private readonly IPkgCmsFileManager $fileManager) {}

    public function demo(): void
    {
        // Create a directory (recursive)
        $this->fileManager->mkdir('/path/to/newdir', true);

        // Copy a file
        $this->fileManager->copy('/tmp/source.txt', '/tmp/dest.txt');

        // Move (rename) a file
        $this->fileManager->move('/tmp/dest.txt', '/tmp/final.txt');

        // Delete a file
        $this->fileManager->delete('/tmp/final.txt');

        // Create a symbolic link
        $this->fileManager->symlink('/path/target', '/path/link');

        // Change file permissions
        $this->fileManager->chmod('/path/target', 0644);

        // Remove a directory and contents
        $this->fileManager->deldir('/path/old', true);

        // Stream-based write
        $fh = $this->fileManager->fopen('/tmp/log.txt', 'ab');
        $this->fileManager->fwrite($fh, "Log entry\n");
        $this->fileManager->fclose($fh);
    }
}
```
Deprecated
----------
This bundle is deprecated since Chameleon System 8.0.0. Use `Symfony\\Component\\Filesystem\\Filesystem` directly in new code.

License
-------
This bundle is licensed under the MIT License. See the `LICENSE` file at the project root for details.