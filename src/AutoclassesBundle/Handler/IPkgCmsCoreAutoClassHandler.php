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
     *
     * @param string $sClassName
     * @param string $targetDir
     *
     * @return void
     */
    public function create($sClassName, $targetDir);

    /**
     * converts the key under which the auto class definition is stored into the class name which the key stands for.
     *
     * @param string $sKey
     *
     * @return string
     */
    public function getClassNameFromKey($sKey);

    /**
     * returns true if the auto class handler knows how to handle the class name passed.
     *
     * @param string $sClassName
     *
     * @return bool
     */
    public function canHandleClass($sClassName);

    /**
     * return an array holding classes the handler is responsible for.
     *
     * @return array
     */
    public function getClassNameList();

    /**
     * resets the internal cache (e.g. for the glue mapping)
     * Call this when recreating classes.
     *
     * @return void
     */
    public function resetInternalCache();
}
