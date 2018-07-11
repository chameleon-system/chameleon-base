<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgCmsFileManager_FileSystem implements IPkgCmsFileManager
{
    /**
     * {@inheritdoc}
     */
    public function put($sSource, $sTarget, $chmod = null, $remoteOnly = false)
    {
        // only do something when we are local
        if (!$remoteOnly) {
            $ret = rename($sSource, $sTarget);
            if (null !== $chmod) {
                $this->chmod($sTarget, $chmod);
            }

            return $ret;
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
        if (is_link($source)) {
            return symlink(readlink($source), $target);
        }
        if (is_file($source)) {
            return copy($source, $target);
        }
        if (!file_exists($target)) {
            mkdir($target);
        }
        $dir = dir($source);
        $retValue = true;
        while (false !== $element = $dir->read()) {
            if ('.' === $element || '..' === $element) {
                continue;
            }
            $subRetValue = $this->copy($source.DIRECTORY_SEPARATOR.$element, $target.DIRECTORY_SEPARATOR.$element);
            $retValue = $subRetValue && $retValue;
        }
        $dir->close();

        return $retValue;
    }

    /**
     * {@inheritdoc}
     */
    public function get($sPath)
    {
        // TODO: Implement get() method.
    }

    /**
     * {@inheritdoc}
     */
    public function delete($file)
    {
        return $this->unlink($file);
    }

    /**
     * {@inheritdoc}
     */
    public function unlink($file)
    {
        return unlink($file);
    }

    /**
     * {@inheritdoc}
     */
    public function symlink($target, $link)
    {
        return symlink($target, $link);
    }

    /**
     * {@inheritdoc}
     */
    public function setDriver(IClusterDriver $driver)
    {
        // we don't need a driver
    }

    /**
     * {@inheritdoc}
     */
    public function chmod($file, $mode)
    {
        return chmod($file, $mode);
    }

    /**
     * {@inheritdoc}
     */
    public function mkdir($path, $mode = 0777, $recursive = false)
    {
        if (0777 !== $mode) {
            trigger_error('File permissions are handled by the administrator. Do not try to set the mode by hand.', E_USER_DEPRECATED);
        }

        return mkdir($path, 0777, $recursive);
    }

    /**
     * {@inheritdoc}
     */
    public function rmdir($path)
    {
        return rmdir($path);
    }

    /**
     * {@inheritdoc}
     */
    public function deldir($path, $recursive = false)
    {
        $path = realpath($path);
        if (false === $path || false === is_dir($path)) {
            return;
        }
        $files = glob($path.'/*');
        if (false === $files && is_dir($path) && is_readable($path) && is_writable($path)) {
            $files = array(); //fix for debian systems, which don't return an empty array, but false, when they find an empty folder.
        }
        foreach ($files as $file) {
            if (is_dir($file) && $recursive) {
                $this->deldir($file, $recursive);
            } else {
                $this->unlink($file);
            }
        }
        $this->rmdir($path);
    }

    /**
     * {@inheritdoc}
     */
    public function fopen($filename, $mode, $use_include_path = null)
    {
        if (null === $use_include_path) {
            return fopen($filename, $mode); // don't ask
        }

        return fopen($filename, $mode, $use_include_path);
    }

    /**
     * {@inheritdoc}
     */
    public function fwrite($handle, $string, $length = null)
    {
        if (null === $length) {
            return fwrite($handle, $string); // I said don't ask!
        }

        return fwrite($handle, $string, $length);
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
        return file_put_contents($filename, $data);
    }
}
