<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\UpdateCounterMigrationBundle\DataAccess;

use Symfony\Component\HttpKernel\KernelInterface;

class BundleDataAccess implements BundleDataAccessInterface
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function getBundlePaths(): array
    {
        $paths = [];
        foreach ($this->kernel->getBundles() as $bundle) {
            $paths[$bundle->getName()] = $bundle->getPath();
        }

        return $paths;
    }
}
