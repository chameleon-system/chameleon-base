<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\DatabaseMigration\Util;

/**
 * Class MigrationPathUtil.
 */
interface MigrationPathUtilInterface
{
    /**
     * @param string $pathToBundle - absolute path
     *
     * @return string[]
     */
    public function getUpdateFoldersFromBundlePath($pathToBundle);

    /**
     * @param string $updateFolder - absolute path
     *
     * @return string[]
     */
    public function getUpdateFilesFromFolder($updateFolder);

    /**
     * @param string $updateFile - filename
     *
     * @return int
     *
     * @throws \InvalidArgumentException
     */
    public function getBuildNumberFromUpdateFile($updateFile);
}
