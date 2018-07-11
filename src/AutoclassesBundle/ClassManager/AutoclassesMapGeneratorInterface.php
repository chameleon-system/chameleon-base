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

/**
 * Interface AutoclassesMapGeneratorInterface defines a service that generates a class map for Chameleon autoclasses.
 */
interface AutoclassesMapGeneratorInterface
{
    /**
     * Returns a class map of all classes within the given $autoclassesDir.
     *
     * @param string $autoclassesDir the directory in which autoclasses are searched
     *
     * @return array a list of autoclasses (mapping class names to their types)
     */
    public function generateAutoclassesMap($autoclassesDir);
}
