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
use IPkgCmsFileManager;

abstract class TPkgCoreAutoClassHandler_AbstractBase implements IPkgCmsCoreAutoClassHandler
{
    protected $aClassMapping = null;
    protected $aClassExtensionList = null;
    protected $aClassNameList = null;
    /**
     * @var IPkgCmsFileManager
     */
    protected $filemanager;
    /**
     * @var Connection
     */
    private $databaseConnection;

    public function __construct(Connection $databaseConnection, IPkgCmsFileManager $filemanager)
    {
        $this->databaseConnection = $databaseConnection;
        $this->filemanager = $filemanager;
    }

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
