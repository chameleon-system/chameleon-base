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

use InvalidArgumentException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * {@inheritdoc}
 */
class MigrationPathUtil implements MigrationPathUtilInterface
{
    const UPDATE_FILENAME_PATTERN_WITH_GROUPED_BUILDNUMBER = '/.*?(\d+)\.inc\.php$/';

    /**
     * @var array
     */
    private $pathsInBundle = array();

    /**
     * @param string $pathInBundle
     *
     * @return void
     */
    public function addPathToUpdatesInBundle($pathInBundle)
    {
        $this->pathsInBundle[] = $pathInBundle;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdateFoldersFromBundlePath($pathToBundle)
    {
        if (0 === count($this->pathsInBundle)) {
            return array();
        }
        if (false === file_exists($pathToBundle) || false === is_dir($pathToBundle)) {
            return array();
        }

        $collectedUpdateFolders = array();
        $finder = new Finder();
        $finder->directories()->in($pathToBundle);
        foreach ($this->pathsInBundle as $pathInBundle) {
            $finder->path($pathInBundle);
        }
        foreach ($finder as $file) {
            /** @var $file SplFileInfo */
            $collectedUpdateFolders[] = $file->getPath().DIRECTORY_SEPARATOR.$file->getFilename();
        }

        return $collectedUpdateFolders;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdateFilesFromFolder($updateFolder)
    {
        if (false === file_exists($updateFolder) || false === is_dir($updateFolder)) {
            return array();
        }
        $updateFiles = array();
        $finder = new Finder();
        $finder->name(self::UPDATE_FILENAME_PATTERN_WITH_GROUPED_BUILDNUMBER)->in($updateFolder)->depth(0);
        foreach ($finder as $file) {
            /** @var $file SplFileInfo */
            $updateFiles[] = $file->getPath().DIRECTORY_SEPARATOR.$file->getFilename();
        }

        return $updateFiles;
    }

    /**
     * {@inheritdoc}
     */
    public function getBuildNumberFromUpdateFile($updateFile)
    {
        $matches = array();
        preg_match(self::UPDATE_FILENAME_PATTERN_WITH_GROUPED_BUILDNUMBER, $updateFile, $matches);

        if (2 > count($matches)) {
            throw new InvalidArgumentException('File does not contain a build number: '.$updateFile);
        }

        return (int) $matches[1];
    }
}
