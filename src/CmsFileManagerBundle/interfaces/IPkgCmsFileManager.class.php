<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

interface IPkgCmsFileManager
{
    /**
     * @param string   $sSource
     * @param string   $sTarget
     * @param int|null $chmod
     * @param bool     $remoteOnly @deprecated since 6.2.0 - cluster file handling is no longer available.
     *
     * @return bool|null
     */
    public function put($sSource, $sTarget, $chmod = null, $remoteOnly = false);

    /**
     * @param string $source
     * @param string $target
     *
     * @return bool|null
     */
    public function move($source, $target);

    /**
     * @param string $source
     * @param string $target
     *
     * @return bool
     */
    public function copy($source, $target);

    /**
     * @param string $sPath
     *
     * @return string|null
     */
    public function get($sPath);

    /**
     * @param string $file
     *
     * @return bool
     */
    public function delete($file);

    /**
     * @param string $file
     *
     * @return bool
     */
    public function unlink($file);

    /**
     * @param string $target
     * @param string $link
     *
     * @return bool
     */
    public function symlink($target, $link);

    /**
     * @param string $file
     * @param int    $mode
     *
     * @return bool
     */
    public function chmod($file, $mode);

    /**
     * @param string $path
     * @param int    $mode      @deprecated since 6.0.0 - file permissions are handled externally.
     * @param bool   $recursive
     *
     * @return bool
     */
    public function mkdir($path, $mode = 0777, $recursive = false);

    /**
     * @param string $path
     *
     * @return bool
     */
    public function rmdir($path);

    /**
     * @param string $path
     * @param bool   $recursive
     *
     * @return void
     */
    public function deldir($path, $recursive = false);

    /**
     * @param string $filename
     * @param string $mode
     * @param null   $use_include_path
     *
     * @return resource|bool
     */
    public function fopen($filename, $mode, $use_include_path = null);

    /**
     * @param resource $handle
     * @param string   $string
     * @param int|null $length
     *
     * @return int|bool
     */
    public function fwrite($handle, $string, $length = null);

    /**
     * @param resource $handle
     *
     * @return bool
     */
    public function fclose($handle);

    /**
     * @param string                $filename
     * @param string|array|resource $data
     *
     * @return int|bool
     */
    public function file_put_contents($filename, $data);
}
