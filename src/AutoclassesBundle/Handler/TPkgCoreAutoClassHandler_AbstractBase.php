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

use ChameleonSystem\AutoclassesBundle\DataAccess\AutoclassesDataAccessInterface;
use Doctrine\DBAL\Connection;
use IPkgCmsFileManager;
use Psr\Log\LoggerInterface;

abstract class TPkgCoreAutoClassHandler_AbstractBase implements IPkgCmsCoreAutoClassHandler
{
    /**
     * @var mixed
     */
    protected $aClassMapping = null;

    protected ?array $aClassExtensionList = null;

    /**
     * @var null|class-string[]
     */
    protected ?array $aClassNameList = null;

    protected IPkgCmsFileManager $fileManager;

    private Connection $databaseConnection;
    private LoggerInterface $logger;
    private AutoclassesDataAccessInterface $autoClassesDataAccess;

    public function __construct(Connection $databaseConnection, IPkgCmsFileManager $fileManager, LoggerInterface $logger, AutoclassesDataAccessInterface $autoClassesDataAccess)
    {
        $this->databaseConnection = $databaseConnection;
        $this->fileManager = $fileManager;
        $this->logger = $logger;
        $this->autoClassesDataAccess = $autoClassesDataAccess;
    }

    public function resetInternalCache(): void
    {
        $this->aClassMapping = null;
        $this->aClassExtensionList = null;
        $this->aClassNameList = null;
    }

    protected function getDatabaseConnection(): Connection
    {
        return $this->databaseConnection;
    }

    protected function getLogger(): LoggerInterface
    {
        return $this->logger;
    }


    protected function getAutoClassesDataAccess(): AutoclassesDataAccessInterface
    {
        return $this->autoClassesDataAccess;
    }
}
