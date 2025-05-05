<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgCmsFileManager_Utilities
{
    /**
     * @param int $input_size - raw input file size in bytes
     *
     * @return int - base64 encoded output file size in bytes
     */
    public function getStepSizeFromBlockSize($input_size)
    {
        $code_size = (($input_size * 4) / 3);
        $padding_size = ($input_size % 3) ? (3 - ($input_size % 3)) : 0;

        /** @var int $total_size */
        $total_size = ceil($code_size + $padding_size);

        return $total_size;
    }

    /**
     * Debian has the wonderful effect of returning false instead of an empty array, if you scan an empty directory with glob. Lovely.
     * To counter this, you may use this method.
     *
     * Please note, that this is NOT a direct replacement of glob, as you need to provide it with the basepath and the pattern instead of just
     * a pattern. So its uses are limited, but in certain cases it will prove useful.
     *
     * Example use:
     *
     * $result = debianSaveGlob("/path/to/my/cache", "*.inc.php"); // will do a glob for the pattern "/path/to/my/cache/*.inc.php"
     *
     * @param string $path
     * @param string $patternInPath
     * @param int|null $flags
     *
     * @return array
     */
    public function debianSaveGlob($path, $patternInPath, $flags = null)
    {
        $pattern = $path.DIRECTORY_SEPARATOR.$patternInPath;
        $result = glob($pattern, $flags);
        if (false === $result && is_dir($path) && is_readable($path) && is_writable($path)) {
            $result = []; // fix for debian systems, which don't return an empty array, but false, when they find an empty folder.
        }

        return $result;
    }
}
