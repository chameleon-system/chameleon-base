<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle;

use Symfony\Component\HttpKernel\Kernel;

abstract class ChameleonAppKernel extends Kernel
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        if (true === $this->booted) {
            return;
        }

        parent::boot();

        ServiceLocator::setContainer($this->container);
        $this->booted = true;
    }
}
