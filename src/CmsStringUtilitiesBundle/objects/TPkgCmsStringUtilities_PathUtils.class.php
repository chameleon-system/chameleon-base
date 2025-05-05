<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgCmsStringUtilities_PathUtils
{
    /**
     * it will be checked, if the given path stays below its root (eg. foo/../bar is ok, foo/../../bar is not).
     *
     * @param string $path
     *
     * @return bool - will return true, if it is ok, will return false wither if it's not ok, or if the path is absolute
     */
    public static function isSaveRelativePath($path)
    {
        if (false === self::isRelativePath($path)) {
            return false;
        }

        $parts = explode('/', $path);
        $level = 0;
        foreach ($parts as $part) {
            if ('..' === $part) {
                --$level;
                if ($level < 0) {
                    return false;
                }
            } elseif ('' !== $part) {
                ++$level;
            }
        }

        return true;
    }

    /**
     * @var bool used to test isSaveBelowDocumentRoot
     */
    public static $saveBacauseOfRealPathCheck = false;

    /**
     * Checks if the given path lands below the setting of document_root (or any given). It will keep symlinks in place if realpath can't be used.
     *
     * This is useful for servers, which have a subfolder of httpdocs placed somewhere else and linked. in theses cases, doing
     * a check with realpath will fail horribly.
     *
     * @param string $path - the path to check
     * @param string $alternativePath - if you don't want to check against document root, you may provide an alternative path here
     *
     * @return bool
     */
    public static function isSaveBelowDocumentRoot($path, $alternativePath = null)
    {
        self::$saveBacauseOfRealPathCheck = false;

        $docRoot = $_SERVER['DOCUMENT_ROOT'];
        if (null !== $alternativePath) {
            $docRoot = $alternativePath;
        }

        if (realpath($path) && realpath($docRoot)) {
            $realTestPath = realpath($path);
            $realDocumentRoot = realpath($docRoot);
            if (strlen($realTestPath) >= strlen($realDocumentRoot)) {
                if (substr($realTestPath, 0, strlen($realDocumentRoot)) === $realDocumentRoot) {
                    self::$saveBacauseOfRealPathCheck = true;

                    return true;
                }
            }
        }

        if ('/' === substr($path, 0, 1)) {
            $path = substr($path, 1);
        }
        $pathsegments = explode('/', $path);
        $documentrootSegments = explode('/', $docRoot);

        array_shift($documentrootSegments);

        $pathstack = [];
        foreach ($pathsegments as $segment) {
            if ('..' === $segment) {
                if (0 === count($pathstack)) {
                    return false;
                } else {
                    array_pop($pathstack);
                }
            } else {
                $pathstack[] = $segment;
            }
        }

        if (count($pathstack) < count($documentrootSegments)) {
            return false;
        }

        $i = 0;
        foreach ($documentrootSegments as $documentrootSegment) {
            if ($pathstack[$i++] !== $documentrootSegment) {
                return false;
            }
        }

        return true;
    }

    /**
     * checks if a path is relative.
     *
     * @param string $string
     *
     * @return bool
     */
    public static function isRelativePath($string)
    {
        if ('' === $string) {
            return true;
        }

        return DIRECTORY_SEPARATOR !== substr($string, 0, 1);
    }
}
