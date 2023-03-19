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

use ChameleonSystem\AutoclassesBundle\DataAccess\AutoclassesDataAccessInterface;
use ChameleonSystem\AutoclassesBundle\Exception\TPkgCmsCoreAutoClassManagerException_Recursion;
use ChameleonSystem\AutoclassesBundle\Handler\IPkgCmsCoreAutoClassHandler;
use ChameleonSystem\AutoclassesBundle\Handler\TPkgCoreAutoClassHandler_AbstractBase;
use ChameleonSystem\AutoclassesBundle\Handler\TPkgCoreAutoClassHandler_TableClass;
use ChameleonSystem\AutoclassesBundle\Handler\TPkgCoreAutoClassHandler_TPkgCmsClassManager;
use Doctrine\DBAL\Connection;
use IPkgCmsFileManager;
use Psr\Log\LoggerInterface;

/**
 * {@inheritdoc}
 */
class AutoclassesManager implements AutoclassesManagerInterface
{
    /**
     * @var TPkgCoreAutoClassHandler_AbstractBase[]
     */
    private array $handlerList = [];

    /**
     * to prevent infinite recursion we push each class being processed onto the callstack - and pop it back out when it has been generated.
     */
    private array $callStack = [];

    public function __construct(Connection $databaseConnection, IPkgCmsFileManager $fileManager, LoggerInterface $logger, AutoclassesDataAccessInterface $autoClassesDataAccess)
    {
        $this->registerHandler(new TPkgCoreAutoClassHandler_TableClass($databaseConnection, $fileManager, $logger, $autoClassesDataAccess));
        $this->registerHandler(new TPkgCoreAutoClassHandler_TPkgCmsClassManager($databaseConnection, $fileManager, $logger, $autoClassesDataAccess));
    }

    /**
     * {@inheritdoc}
     */
    public function registerHandler(IPkgCmsCoreAutoClassHandler $handler): void
    {
        $this->handlerList[] = $handler;
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $classname, string $targetDir): bool
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

    private function isInCallStack(string $className): bool
    {
        return in_array($className, $this->callStack);
    }

    private function pushToCallStack(string $className): void
    {
        $this->callStack[] = $className;
    }

    private function popFromCallStack(): string
    {
        return array_pop($this->callStack);
    }
}
