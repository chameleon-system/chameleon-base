<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\UpdateCounterMigrationBundle\Config;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpKernel\KernelInterface;

class MigrationConfigGenerator
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @return array
     */
    public function getMigrationConfigData()
    {
        $bundle = $this->kernel->getBundle('EsonoCustomerBundle');
        $customerPath = $bundle->getPath();
        $customerUpdatePath = $customerPath.'/extensions/updates';

        $bundlesForCustomer = $this->getBundlesForCustomer($customerPath.'/../');

        $migrationConfigData = array();
        $directoriesToMigrate = $this->getDirectoriesToMigrate($customerUpdatePath);
        foreach ($directoriesToMigrate as $dir) {
            $oldCounterName = "dbversion-meta-customer-$dir";
            $newCounterName = $this->getNewCounterName($bundlesForCustomer, $customerUpdatePath, $dir);
            $migrationConfigData[$oldCounterName] = $newCounterName;
        }

        return $migrationConfigData;
    }

    /**
     * @param string $bundleBasePath
     *
     * @return array
     */
    private function getBundlesForCustomer($bundleBasePath)
    {
        $list = array();
        if (false === $handle = opendir($bundleBasePath)) {
            return $list;
        }
        while (false !== $entry = readdir($handle)) {
            if ('.' === $entry || '..' === $entry) {
                continue;
            }
            if (is_dir($bundleBasePath.'/'.$entry)) {
                $list[$entry] = 1;
            }
        }

        return $list;
    }

    /**
     * @param string $updatePath
     *
     * @return array
     */
    private function getDirectoriesToMigrate($updatePath)
    {
        $dirList = array();
        if ($handle = opendir($updatePath)) {
            while (false !== $entry = readdir($handle)) {
                if (is_link($updatePath.'/'.$entry)) {
                    $dirList[] = $entry;
                }
            }
            closedir($handle);
        } else {
            throw new FileNotFoundException("Update path could not be opened: $updatePath");
        }

        return $dirList;
    }

    /**
     * @param string $updatePath
     * @param string $symlinkName
     * @param array $bundleNames
     *
     * @return string
     */
    private function getNewCounterName($bundleNames, $updatePath, $symlinkName)
    {
        $symlinkPath = $updatePath.'/'.$symlinkName;
        $realPath = realpath(dirname(realpath($symlinkPath)));
        $stopPath = basename(realpath(PATH_PROJECT_BASE));

        $bundleName = null;
        while ('/' !== $realPath) {
            $dirName = basename($realPath);
            if (isset($bundleNames[$dirName])) {
                $bundleName = $dirName;
                break;
            }

            if ($dirName === $stopPath) {
                break;
            }
            $realPath = dirname($realPath);
            echo $realPath."\n";
        }

        $parentDirName = basename(dirname($realPath));
        $realDirName = basename($realPath);

        return "dbversion-meta-packages-$parentDirName/$realDirName";
    }
}
