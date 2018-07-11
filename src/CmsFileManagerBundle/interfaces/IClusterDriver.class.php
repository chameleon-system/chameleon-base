<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @deprecated since 6.2.0 - cluster file handling is no longer available.
 */
interface IClusterDriver
{
    public function syncFile($source, $target, $mode, $remoteOnly = false);

    public function addNode($hostName);

    public function removeNode($hostName);

    public function addCredentials($credentials);

    public function chmod($file, $mode);

    public function unlink($file);

    public function symlink($target, $link);

    public function copy($source, $target);

    public function rename($source, $target);

    public function mkdir($path, $mode, $recursive);

    public function rmdir($path);

    public function deldir($path, $recursive);
}
