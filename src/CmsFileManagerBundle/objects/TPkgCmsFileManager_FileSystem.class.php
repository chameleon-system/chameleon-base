<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @deprecated since 8.0.0 - use symfony filesystem component instead
 */
class TPkgCmsFileManager_FileSystem implements IPkgCmsFileManager
{
    private Filesystem $symfonyFileSystem;

    public function __construct()
    {
        $this->symfonyFileSystem = new Filesystem();
    }

    /**
     * {@inheritdoc}
     */
    public function put($sSource, $sTarget, $chmod = null)
    {
        try {
            $this->symfonyFileSystem->rename($sSource, $sTarget, true);
            if (null !== $chmod) {
                $this->chmod($sTarget, $chmod);
            }

            return true;
        } catch (IOExceptionInterface $exception) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function move($source, $target)
    {
        return $this->put($source, $target);
    }

    /**
     * {@inheritdoc}
     */
    public function copy($source, $target)
    {
        try {
            if (is_dir($source)) {
                $this->symfonyFileSystem->mirror($source, $target);
            } else {
                $this->symfonyFileSystem->copy($source, $target, true);
            }

            return true;
        } catch (IOExceptionInterface $exception) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function delete($file)
    {
        try {
            $this->symfonyFileSystem->remove($file);

            return true;
        } catch (IOExceptionInterface $exception) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function unlink($file)
    {
        return $this->delete($file);
    }

    /**
     * {@inheritdoc}
     */
    public function symlink($target, $link)
    {
        try {
            $this->symfonyFileSystem->symlink($target, $link, true);

            return true;
        } catch (IOExceptionInterface $exception) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function chmod($file, $mode)
    {
        try {
            $this->symfonyFileSystem->chmod($file, $mode);

            return true;
        } catch (IOExceptionInterface $exception) {
            return false;
        }
    }

    public function mkdir(string $path, bool $recursive = false): bool
    {
        try {
            $this->symfonyFileSystem->mkdir($path);

            return true;
        } catch (IOExceptionInterface $exception) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rmdir($path)
    {
        return $this->delete($path);
    }

    /**
     * {@inheritdoc}
     */
    public function deldir($path, $recursive = false)
    {
        if ($recursive) {
            return $this->delete($path);
        }

        try {
            $this->symfonyFileSystem->remove($path);

            return true;
        } catch (IOExceptionInterface $exception) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function fopen($filename, $mode, $use_include_path = null)
    {
        return fopen($filename, $mode, $use_include_path ?? false);
    }

    /**
     * {@inheritdoc}
     */
    public function fwrite($handle, $string, $length = null)
    {
        return fwrite($handle, $string, $length ?? strlen($string));
    }

    /**
     * {@inheritdoc}
     */
    public function fclose($handle)
    {
        return fclose($handle);
    }

    /**
     * {@inheritdoc}
     */
    public function file_put_contents($filename, $data)
    {
        try {
            file_put_contents($filename, $data);

            return true;
        } catch (Exception $exception) {
            return false;
        }
    }
}
