<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\DatabaseMigrationBundle\Bridge\Chameleon\Recorder;

use ChameleonSystem\DatabaseMigration\Constant\MigrationRecorderConstants;
use ChameleonSystem\DatabaseMigration\DataModel\LogChangeDataModel;
use Doctrine\DBAL\Connection;
use MapperException;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TPkgSnippetRenderer_SnippetRenderingException;
use ViewRenderer;

class MigrationRecorder
{
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var Connection
     */
    private $databaseConnection;
    /**
     * @var QueryWriter
     */
    private $queryWriter;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var string
     */
    private $logFilePath;

    /**
     * @param ContainerInterface $container
     * @param Connection         $databaseConnection
     * @param QueryWriter        $queryWriter
     * @param LoggerInterface    $logger
     * @param string             $logFilePath
     */
    public function __construct(ContainerInterface $container, Connection $databaseConnection, QueryWriter $queryWriter, LoggerInterface $logger, $logFilePath)
    {
        $this->container = $container; // used to get the non-shared snippet renderer service
        $this->databaseConnection = $databaseConnection;
        $this->queryWriter = $queryWriter;
        $this->logger = $logger;
        $this->logFilePath = $logFilePath;
    }

    /**
     * @param string $activeTrackName @deprecated since 6.2.0 - a fixed name is used now.
     * @param string $buildNumber
     *
     * @return resource
     *
     * @throws MapperException
     * @throws TPkgSnippetRenderer_SnippetRenderingException
     */
    public function startTransation($activeTrackName, $buildNumber)
    {
        $counterName = MigrationRecorderConstants::MIGRATION_SCRIPT_NAME;

        $sLogFileName = sprintf('%s/%s-%s.inc.php', $this->logFilePath, $counterName, $buildNumber);
        if (!file_exists($this->logFilePath)) {
            mkdir($this->logFilePath, 0777, true);
        }
        $head = null;
        if (!file_exists($sLogFileName)) {
            $viewRenderer = $this->getViewRenderer();
            $viewRenderer->AddSourceObject('buildnumber', $buildNumber);
            $viewRenderer->AddSourceObject('date', date('Y-m-d'));
            $viewRenderer->setShowHTMLHints(false);
            $head = $viewRenderer->Render('MigrationRecorder/migrationFileTemplate.html.twig');
        }
        if ($filePointer = fopen($sLogFileName, 'ab')) {
            if (null !== $head) {
                fwrite($filePointer, $head, strlen($head));
            }
        } else {
            $this->logger->error(
                sprintf('File %s is not writable (check path constant: PATH_CMS_CHANGE_LOG, and missing rights for file writes).', $sLogFileName)
            );

            exit(); // we want to break the ajax call to get the warning
        }

        return $filePointer;
    }

    /**
     * @param resource             $filePointer
     * @param LogChangeDataModel[] $dataModels
     *
     * @return void
     */
    public function writeQueries($filePointer, array $dataModels)
    {
        $this->queryWriter->writeQueries($filePointer, $dataModels);
    }

    /**
     * @param resource $filePointer
     *
     * @return void
     */
    public function endTransaction($filePointer)
    {
        fclose($filePointer);
        $this->databaseConnection->exec('UNLOCK TABLES');
    }

    /**
     * @return ViewRenderer
     */
    private function getViewRenderer()
    {
        $viewRenderer = $this->container->get('chameleon_system_view_renderer.view_renderer');
        $viewRenderer->setShowHTMLHints(false);

        return $viewRenderer;
    }
}
