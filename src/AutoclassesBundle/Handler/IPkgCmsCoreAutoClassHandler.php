<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\AutoclassesBundle\Handler;

interface IPkgCmsCoreAutoClassHandler
{
    /**
     * create the auto class.
     */
    public function create(string $sClassName, string $targetDir): void;

    /**
     * converts the key under which the auto class definition is stored into the class name which the key stands for.
     *
     * @return string|false
     */
    public function getClassNameFromKey(string $sKey);

    /**
     * returns true if the auto class handler knows how to handle the class name passed.
     */
    public function canHandleClass(string $sClassName): bool;

    /**
     * return an array holding classes the handler is responsible for.
     */
    public function getClassNameList(): array;

    /**
     * resets the internal cache (e.g. for the glue mapping)
     * Call this when recreating classes.
     */
    public function resetInternalCache(): void;
}
