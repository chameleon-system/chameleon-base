<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\CoreEvents;
use ChameleonSystem\CoreBundle\Event\UpdateBeforeMigrationFilesCollectionEvent;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\DatabaseMigration\Constant\DatabaseMigrationConstants;
use ChameleonSystem\DatabaseMigration\Counter\MigrationCounterManagerInterface;
use ChameleonSystem\DatabaseMigration\DataModel\ErrorQuery;
use ChameleonSystem\DatabaseMigration\DataModel\MigrationDataModel;
use ChameleonSystem\DatabaseMigration\DataModel\MigrationResult;
use ChameleonSystem\DatabaseMigration\DataModel\SuccessQuery;
use ChameleonSystem\DatabaseMigration\DataModel\UpdateException;
use ChameleonSystem\DatabaseMigration\DataModel\UpdateMessage;
use ChameleonSystem\DatabaseMigration\Factory\MigrationDataModelFactoryInterface;
use ChameleonSystem\DatabaseMigration\Reducer\MigrationDataModelReducer;
use ChameleonSystem\DatabaseMigration\Util\MigrationPathUtil;
use ChameleonSystem\DatabaseMigrationBundle\Bridge\Chameleon\Converter\DataModelConverter;
use ChameleonSystem\DatabaseMigrationBundle\Bridge\Chameleon\MigrationDataModelFactory\ChameleonProcessedMigrationDataModelFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * update file management, loads and executes database updates.
 *
 * /**/
class TCMSUpdateManager
{
    /**
     * @var array
     */
    private $updateMessages = [];
    /**
     * @var array
     */
    private $successQueries = [];
    /**
     * @var array
     */
    private $errorQueries = [];
    /**
     * @var array
     */
    private $exceptions = [];

    /**
     * needs to be overwritten in the child class. should return a pointer to
     * an instance of the child global class.
     *
     * @return TCMSUpdateManager
     */
    public static function GetInstance()
    {
        static $oUpdateManager;
        if (!$oUpdateManager) {
            $oUpdateManager = new self();
        }

        return $oUpdateManager;
    }

    /**
     * returns all update files for all types that have not been processed yet.
     *
     * @return array[]
     */
    public function getAllUpdateFilesToProcess()
    {
        $completeDataModels = $this->getAllMigrationDataModels();
        $processedMigrationDataModels = $this->getChameleonProcessedMigrationDataModelFactory()->createMigrationDataModels();

        $modelsToRun = $this->getDataModelReducer()->reduceModelListByModelList($completeDataModels, $processedMigrationDataModels);

        return $this->getDataModelConverter()->convertDataModelsToLegacySystem($modelsToRun);
    }

    /**
     * @return MigrationDataModel[]
     */
    private function getAllMigrationDataModels()
    {
        $this->getEventDispatcher()->dispatch(new UpdateBeforeMigrationFilesCollectionEvent(), CoreEvents::UPDATE_BEFORE_COLLECTION);

        return $this->getFileSystemMigrationDataModelFactory()->createMigrationDataModels();
    }

    /**
     * @param string $bundleName
     * @param int $buildNumber
     *
     * @return bool
     */
    public function isUpdateAlreadyProcessed($bundleName, $buildNumber)
    {
        $processedMigrationData = $this->getProcessedMigrationDataModelFactory()->createMigrationDataModels();

        return
            true === array_key_exists($bundleName, $processedMigrationData)
            && true === array_key_exists($buildNumber, $processedMigrationData[$bundleName]->getBuildNumberToFileMap());
    }

    /**
     * returns the build nr. (not db rev. number!) from file.
     *
     * @param string $sFileName
     *
     * @return int
     */
    public function GetBuildNumberFromFileName($sFileName)
    {
        return $this->getMigrationPathUtil()->getBuildNumberFromUpdateFile($sFileName);
    }

    /**
     * Processes all non-processed updates for a specified bundle up to a buildNumber, or all updates of a bundle if null is passed as
     * highest build number, or updates for all bundles if null is passed as bundle alias.
     *
     * @param string|null $bundleNameToRun
     * @param int|null $highestBuildNumberToExecute
     *
     * @return string
     *
     * @throws InvalidArgumentException if an invalid bundle name was passed
     */
    public function runUpdates($bundleNameToRun = null, $highestBuildNumberToExecute = null)
    {
        if (!defined('CMSUpdateManagerRunning')) {
            define('CMSUpdateManagerRunning', true);
        }

        if ('' === $bundleNameToRun) {
            return '';
        }

        $allUpdateFilesToProcess = $this->getAllUpdateFilesToProcess();

        if (null !== $bundleNameToRun) {
            if (false === isset($allUpdateFilesToProcess[$bundleNameToRun])) {
                $this->getKernel()->getBundle($bundleNameToRun); // will throw an exception if the bundle is not registered

                return ''; // at this point we know that the bundle is registered, but there are simply no updates to execute
            }
            $allUpdateFilesToProcess = $allUpdateFilesToProcess[$bundleNameToRun];
        }
        if (0 === count($allUpdateFilesToProcess)) {
            return '';
        }

        $translator = $this->getTranslator();
        $updateFileResults = [];

        foreach ($allUpdateFilesToProcess as $updateFile) {
            if (null === $highestBuildNumberToExecute || $updateFile->buildNumber <= $highestBuildNumberToExecute) {
                $tmp = clone $updateFile;
                $tmp->fileReturn = $this->runSingleUpdate($updateFile->fileName, $updateFile->bundleName);

                $updateFileResults[] = $tmp;
            }
        }

        if (count($updateFileResults) > 0) {
            $result = '<script type="text/javascript">';
            foreach ($updateFileResults as $oUpdateFile) {
                $result .= sprintf('CHAMELEON.UPDATE_MANAGER.addProcessedUpdate("%s", %s, %s);', $oUpdateFile->bundleName, $oUpdateFile->buildNumber, json_encode($oUpdateFile->fileReturn));
            }
            $result .= '</script>';

            $result .= sprintf('<div class="info">%s</div>', $translator->trans('chameleon_system_core.cms_module_update.updates_executed_up_to_build_number', [
                '%bundleName%' => $bundleNameToRun,
                '%highestBuildNumber%' => $highestBuildNumberToExecute,
            ]));

            return $result;
        }

        return '';
    }

    /**
     * run a single update file and builds a JS response object for the file
     * records the file as "processed" in the database.
     *
     * @param string $sFileName string full file name (e.g. "build6.inc.php)
     * @param string|null $bundleName
     * @param string|null $sSubdir @deprecated since 6.2.0 - no longer used
     *
     * @return MigrationResult
     */
    public function runSingleUpdate($sFileName, $bundleName, $sSubdir = null)
    {
        if (!defined('CMSUpdateManagerRunning')) {
            define('CMSUpdateManagerRunning', true);
        }
        $result = new MigrationResult();
        $result->setFileContents('');

        if (empty($sFileName)) {
            $result->setResponseStatus(DatabaseMigrationConstants::RESPONSE_STATE_ERROR);
            $result->setUpdateStatus(DatabaseMigrationConstants::UPDATE_STATE_ERROR);
            $result->setMessage('File name must be set');

            return $result;
        }

        $buildNumber = $this->GetBuildNumberFromFileName($sFileName);

        if ($this->updateIsBlacklisted($bundleName, $buildNumber)) {
            $result->setResponseStatus(DatabaseMigrationConstants::RESPONSE_STATE_SUCCESS);
            $result->setUpdateStatus(DatabaseMigrationConstants::UPDATE_STATE_SKIPPED);
            $result->setMessage($this->getTranslator()->trans('chameleon_system_core.cms_module_update.update_blacklisted_notice', [
                '%fileName%' => $sFileName,
                '%bundleName%' => $bundleName,
            ]));

            return $result;
        }

        $fullUpdateFilePath = $sFileName;

        $result->setResponseStatus(DatabaseMigrationConstants::RESPONSE_STATE_SUCCESS);
        ob_start();
        try {
            if (false === $this->isUpdateAlreadyProcessed($bundleName, $buildNumber)) {
                $this->requireUpdate($fullUpdateFilePath);
            }
            $fileContents = trim(ob_get_clean());
            if (empty($fileContents)) {
                // the update returned no content = update has been skipped (= higher db version counter)
                $result->setUpdateStatus(DatabaseMigrationConstants::UPDATE_STATE_SKIPPED);
            } else {
                $result->setUpdateStatus(DatabaseMigrationConstants::UPDATE_STATE_EXECUTED);
                $result->setFileContents($fileContents);
            }
        } catch (Exception $e) {
            $this->addException($e);
            $result->setFileContents(trim(ob_get_clean()));
            $result->setUpdateStatus(DatabaseMigrationConstants::UPDATE_STATE_EXECUTED);
        }

        $this->getMigrationCounterManager()->markMigrationFileAsProcessed($bundleName, $buildNumber);

        if ('' !== $result->getFileContents()) {
            $result->setFileContents(str_replace(["\n", "\r", "\t"], '', $result->getFileContents()));
        }
        $result->setInfoMessages($this->updateMessages);
        $this->updateMessages = [];
        $result->setExceptions($this->exceptions);
        $this->exceptions = [];
        $result->setSuccessQueries($this->successQueries);
        $this->successQueries = [];
        $result->setErrorQueries($this->errorQueries);
        $this->errorQueries = [];

        $this->renderMessageOutputToResult($result, $bundleName, $sFileName);

        return $result;
    }

    private function renderMessageOutputToResult(MigrationResult $result, string $bundleName, string $fileName): void
    {
        $viewRenderer = $this->getViewRenderer();
        $viewRenderer->AddSourceObject('bundleName', $bundleName);
        $viewRenderer->AddSourceObject('buildNumber', $this->GetBuildNumberFromFileName($fileName));

        if (count($result->getExceptions()) > 0) {
            $viewRenderer->AddSourceObject('messages', $result->getExceptions());
            $result->setRenderedExceptionsGlobal($viewRenderer->Render('CMSUpdateManager/exceptionGlobal.html.twig'));
            $result->setRenderedExceptionsUpdate($viewRenderer->Render('CMSUpdateManager/exceptionUpdate.html.twig'));
        }

        if (count($result->getErrorQueries()) > 0) {
            $viewRenderer->AddSourceObject('messages', $result->getErrorQueries());
            $result->setRenderedErrorQueriesGlobal($viewRenderer->Render('CMSUpdateManager/errorQueriesGlobal.html.twig'));
            $result->setRenderedErrorQueriesUpdate($viewRenderer->Render('CMSUpdateManager/errorQueriesUpdate.html.twig'));
        }

        if (count($result->getInfoMessages()) > 0) {
            $viewRenderer->AddSourceObject('messages', $result->getInfoMessages());
            $result->setRenderedInfoMessagesGlobal($viewRenderer->Render('CMSUpdateManager/infoGlobal.html.twig'));
            $result->setRenderedInfoMessagesUpdate($viewRenderer->Render('CMSUpdateManager/infoUpdate.html.twig'));
        }

        if (count($result->getSuccessQueries()) > 0) {
            $viewRenderer->AddSourceObject('messages', $result->getSuccessQueries());
            $result->setRenderedSuccessQueriesUpdate($viewRenderer->Render('CMSUpdateManager/successQueriesUpdate.html.twig'));
        }
    }

    /**
     * Runs an update. This command is separated into an own method to avoid variable leaking into and out of the script.
     *
     * @param string $fullUpdateFilePath
     */
    private function requireUpdate($fullUpdateFilePath)
    {
        require_once $fullUpdateFilePath;
    }

    /**
     * returns all (!) update files
     * already processed ones are also included.
     *
     * @return array
     */
    public function getAllUpdateFiles()
    {
        $completeDataModels = $this->getAllMigrationDataModels();
        $converter = $this->getDataModelConverter();

        return $converter->convertDataModelsToLegacySystem($completeDataModels);
    }

    /**
     * @param string $bundleName
     *
     * @return array
     */
    public function getAllUpdateFilesForBundle($bundleName)
    {
        $completeDataModels = $this->getAllMigrationDataModels();
        $converter = $this->getDataModelConverter();

        return $converter->convertDataModelsToLegacySystem([
            $completeDataModels[$bundleName],
        ]);
    }

    /**
     * @return array
     */
    public function getUpdateBlacklist()
    {
        if (false === $this->getServiceContainer()->hasParameter('updateBlacklist')) {
            return [];
        }
        $updateBlacklist = ServiceLocator::getParameter('updateBlacklist');

        if (!is_array($updateBlacklist)) {
            return [];
        }

        return $updateBlacklist;
    }

    /**
     * @param string $bundleName
     * @param int $buildNumber
     *
     * @return bool
     */
    private function updateIsBlacklisted($bundleName, $buildNumber)
    {
        $updateBlacklist = $this->getUpdateBlacklist();

        if (!isset($updateBlacklist[$bundleName])) {
            return false;
        }

        foreach ($updateBlacklist[$bundleName] as $blacklistedBuildNumber) {
            if ($buildNumber === $blacklistedBuildNumber) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $updateMessage
     * @param int $level
     */
    public function addUpdateMessage($updateMessage, $level)
    {
        $this->updateMessages[] = new UpdateMessage($updateMessage, $level);
    }

    /**
     * @param string $query
     * @param int $line
     */
    public function addSuccessQuery($query, $line)
    {
        $this->successQueries[] = new SuccessQuery($query, $line);
    }

    /**
     * @param string $query
     * @param int $line
     * @param string $error
     */
    public function addErrorQuery($query, $line, $error)
    {
        $this->errorQueries[] = new ErrorQuery($query, $line, $error);
    }

    public function addException(Exception $exception)
    {
        $this->exceptions[] = new UpdateException($exception);
    }

    /**
     * @return MigrationDataModelFactoryInterface
     */
    private function getFileSystemMigrationDataModelFactory()
    {
        return ServiceLocator::get('chameleon_system_core.database_migration.file_system_factory');
    }

    /**
     * @return MigrationDataModelFactoryInterface
     */
    private function getChameleonProcessedMigrationDataModelFactory()
    {
        return ServiceLocator::get('chameleon_system_core.database_migration.processed_factory');
    }

    /**
     * @return MigrationDataModelReducer
     */
    private function getDataModelReducer()
    {
        return ServiceLocator::get('chameleon_system_core.database_migration.reducer');
    }

    /**
     * @return DataModelConverter
     */
    private function getDataModelConverter()
    {
        return ServiceLocator::get('chameleon_system_core.database_migration.converter');
    }

    /**
     * @return MigrationPathUtil
     */
    private function getMigrationPathUtil()
    {
        return ServiceLocator::get('chameleon_system_core.database_migration.migration_path_util');
    }

    /**
     * @return MigrationCounterManagerInterface
     */
    private function getMigrationCounterManager()
    {
        return ServiceLocator::get('chameleon_system_core.counter.migration_counter_manager');
    }

    /**
     * @return Symfony\Component\EventDispatcher\EventDispatcher
     */
    private function getEventDispatcher()
    {
        return ServiceLocator::get('event_dispatcher');
    }

    /**
     * @return TranslatorInterface
     */
    private function getTranslator()
    {
        return ServiceLocator::get('translator');
    }

    /**
     * @return ContainerInterface
     */
    private function getServiceContainer()
    {
        return ServiceLocator::get('service_container');
    }

    /**
     * @return KernelInterface
     */
    private function getKernel()
    {
        return ServiceLocator::get('kernel');
    }

    /**
     * @return ChameleonProcessedMigrationDataModelFactory
     */
    private function getProcessedMigrationDataModelFactory()
    {
        return ServiceLocator::get('chameleon_system_core.database_migration.processed_factory');
    }

    private function getViewRenderer(): ViewRenderer
    {
        return ServiceLocator::get('chameleon_system_view_renderer.view_renderer');
    }
}
