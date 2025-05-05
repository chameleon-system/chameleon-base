<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\DistributionBundle\VersionCheck\Filter;

class ChameleonPackageFilter implements PackageNameFilterInterface
{
    /**
     * {@inheritDoc}
     */
    public function filter($name)
    {
        $prefix = 'chameleon-system/';

        return substr($name, 0, strlen($prefix)) === $prefix && 'chameleon-system/chameleon-base' !== $name;
    }
}
