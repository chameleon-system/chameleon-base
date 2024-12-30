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

use Doctrine\DBAL\Connection;
use Symfony\Component\Filesystem\Filesystem;

abstract class TPkgCoreAutoClassHandler_AbstractBase implements IPkgCmsCoreAutoClassHandler
{
    protected ?array $aClassMapping = null;
    protected ?array $aClassExtensionList = null;
    protected ?array $aClassNameList = null;
    protected Filesystem $filemanager;
    private Connection $databaseConnection;

    public function __construct(Connection $databaseConnection, Filesystem $filemanager)
    {
        $this->databaseConnection = $databaseConnection;
        $this->filemanager = $filemanager;
    }

    /**
     * {@inheritDoc}
     */
    public function resetInternalCache()
    {
        $this->aClassMapping = null;
        $this->aClassExtensionList = null;
        $this->aClassNameList = null;
    }

    /**
     * @return Connection
     */
    protected function getDatabaseConnection()
    {
        return $this->databaseConnection;
    }
}
