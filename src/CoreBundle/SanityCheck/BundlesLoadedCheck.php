<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\SanityCheck;

use ChameleonSystem\SanityCheck\Check\AbstractCheck;
use ChameleonSystem\SanityCheck\Outcome\CheckOutcome;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Class CacheCheck Checks if caching is activated outside dev mode.
 */
class BundlesLoadedCheck extends AbstractCheck
{
    /**
     * @var Kernel
     */
    private $kernel;

    /**
     * @var string
     */
    private $composerLockPath;

    /**
     * @var string
     */
    private $vendorPath;

    /**
     * @var bool
     */
    private $includeDev;

    /**
     * @param int $level
     * @param Kernel $kernel
     * @param string $composerLockPath
     * @param string $vendorPath
     * @param bool $includeDev
     */
    public function __construct($level, Kernel $kernel, $composerLockPath, $vendorPath, $includeDev)
    {
        parent::__construct($level);
        $this->kernel = $kernel;
        $this->composerLockPath = $composerLockPath;
        $this->vendorPath = $vendorPath;
        $this->includeDev = $includeDev;
    }

    /**
     * @return CheckOutcome[]
     */
    public function performCheck()
    {
        $retValue = array();

        $filePath = $this->composerLockPath.'/composer.lock';
        $composerData = json_decode(file_get_contents($filePath));
        $packages = $composerData->packages;
        $names = array();
        foreach ($packages as $package) {
            $names[] = $package->name;
        }
        if ($this->includeDev) {
            $packages = $composerData->{'packages-dev'};
            foreach ($packages as $package) {
                $names[] = $package->name;
            }
        }

        $composerData = null;
        $packages = null;
        $registeredBundles = $this->kernel->getBundles();
        $registeredBundleNames = array_keys($registeredBundles);

        foreach ($names as $name) {
            $path = $this->vendorPath.DIRECTORY_SEPARATOR.$name;
            if (!is_dir($path)) {
                continue;
            }
            if ($handle = opendir($path)) {
                while (false !== ($file = readdir($handle))) {
                    if ((false !== strpos($file, 'Bundle')) && (false !== strpos($file, '.php'))) {
                        $className = substr($file, 0, strpos($file, '.php'));
                        if (!in_array($className, $registeredBundleNames)) {
                            $retValue[] = new CheckOutcome('check.bundles_loaded.notregistered', array('%0%' => $className), $this->getLevel());
                        }
                    }
                }
                closedir($handle);
            } else {
                $retValue[] = new CheckOutcome('check.bundles_loaded.directorynotopened', array('%0%' => $path), CheckOutcome::EXCEPTION);
            }
        }

        if (empty($retValue)) {
            $retValue[] = new CheckOutcome('check.bundles_loaded.ok', array(), CheckOutcome::OK);
        }

        return $retValue;
    }
}
