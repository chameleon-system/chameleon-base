<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\ModuleService;

interface ModuleResolverInterface
{
    /**
     * @param string $name
     *
     * @return \TModelBase|null
     */
    public function getModule($name);

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasModule($name);
}