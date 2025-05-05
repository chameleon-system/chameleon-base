<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\AutoclassesBundle\ClassManager;

use ChameleonSystem\AutoclassesBundle\Exception\TPkgCmsCoreAutoClassManagerException_Recursion;
use ChameleonSystem\AutoclassesBundle\Handler\IPkgCmsCoreAutoClassHandler;

/**
 * AutoclassesManagerInterface defines a service that manages the creation of Chameleon autoclasses.
 * It delegates the actual work to registered handlers.
 */
interface AutoclassesManagerInterface
{
    /**
     * Creates the structure for the auto class requested.
     *
     * @param string $classname
     * @param string $targetDir
     *
     * @return bool true if the class generation was successful
     *
     * @throws TPkgCmsCoreAutoClassManagerException_Recursion
     */
    public function create($classname, $targetDir);

    /**
     * Registers a handler that is able to create autoclasses of a certain type.
     *
     * @return void
     */
    public function registerHandler(IPkgCmsCoreAutoClassHandler $handler);
}
