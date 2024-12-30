<?php

namespace ChameleonSystem\CoreBundle\Service;

use Symfony\Component\Filesystem\Filesystem;

/**
 * Wrapper for the Symfony Filesystem service to allow it to be loaded via ServiceLocator.
 */
class FileSystemWrapper
{
    public function __construct(private readonly Filesystem $filesystem)
    {
    }

    public function getFileSystemService(): Filesystem
    {
        return $this->filesystem;
    }
}
