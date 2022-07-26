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
use ChameleonSystem\AutoclassesBundle\Handler\TPkgCoreAutoClassHandler_AbstractBase;
use ChameleonSystem\AutoclassesBundle\Handler\TPkgCoreAutoClassHandler_TableClass;
use ChameleonSystem\AutoclassesBundle\Handler\TPkgCoreAutoClassHandler_TPkgCmsClassManager;
use Doctrine\DBAL\Connection;
use IPkgCmsFileManager;

/**
 * {@inheritdoc}
 */
class AutoclassesManager implements AutoclassesManagerInterface
{
    /**
     * @var TPkgCoreAutoClassHandler_AbstractBase[]
     */
    private $handlerList = array();
    /**
     * to prevent infinite recursion we push each class being processed onto the callstack - and pop it back out when it has been generated.
     *
     * @var array
     */
    private $callStack = array();

    /**
     * @param Connection         $databaseConnection
     * @param IPkgCmsFileManager $filemanager
     */
    public function __construct(Connection $databaseConnection, IPkgCmsFileManager $filemanager)
    {
        $this->registerHandler(new TPkgCoreAutoClassHandler_TableClass($databaseConnection, $filemanager));
        $this->registerHandler(new TPkgCoreAutoClassHandler_TPkgCmsClassManager($databaseConnection, $filemanager));
    }

    /**
     * {@inheritdoc}
     */
    public function registerHandler(IPkgCmsCoreAutoClassHandler $handler)
    {
        $this->handlerList[] = $handler;
    }

    /**
     * {@inheritdoc}
     */
    public function create($classname, $targetDir)
    {
        $classCreated = false;
        if (true === $this->isInCallStack($classname)) {
            throw new TPkgCmsCoreAutoClassManagerException_Recursion($classname, $this->callStack, __FILE__, __LINE__);
        }

        $this->pushToCallStack($classname);

        reset($this->handlerList);
        foreach ($this->handlerList as $handler) {
            // continue until we find a matching handler
            if (false === $handler->canHandleClass($classname)) {
                continue;
            }
            $classCreated = true;
            $handler->create($classname, $targetDir);
            break;
        }
        $this->popFromCallStack();

        return $classCreated;
    }

    /**
     * @param string $className
     *
     * @return bool
     */
    private function isInCallStack($className)
    {
        return in_array($className, $this->callStack);
    }

    /**
     * @param string $className
     *
     * @return void
     */
    private function pushToCallStack($className)
    {
        $this->callStack[] = $className;
    }

    /**
     * @return string
     */
    private function popFromCallStack()
    {
        return array_pop($this->callStack);
    }
}
