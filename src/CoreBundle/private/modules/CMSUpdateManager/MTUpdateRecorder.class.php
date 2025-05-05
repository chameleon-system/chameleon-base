<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\DatabaseMigration\Constant\MigrationRecorderConstants;
use ChameleonSystem\DatabaseMigrationBundle\Bridge\Chameleon\Recorder\MigrationRecorderStateHandler;

class MTUpdateRecorder extends AbstractViewMapper
{
    /**
     * @var MigrationRecorderStateHandler
     */
    private $migrationRecorderStateHandler;

    public function __construct(MigrationRecorderStateHandler $migrationRecorderStateHandler)
    {
        $this->migrationRecorderStateHandler = $migrationRecorderStateHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements): void
    {
        $oRequirements->NeedsSourceObject('sModuleSpotName', 'string');
    }

    /**
     * {@inheritdoc}
     */
    public function Accept(
        IMapperVisitorRestricted $oVisitor,
        $bCachingEnabled,
        IMapperCacheTriggerRestricted $oCacheTriggerManager
    ): void {
        $isDatabaseLoggingAllowed = $this->migrationRecorderStateHandler->isDatabaseLoggingAllowed();
        if ($isDatabaseLoggingAllowed) {
            $isLoggingActive = $this->migrationRecorderStateHandler->isDatabaseLoggingActive();
            $currentBuildNumber = $this->migrationRecorderStateHandler->getCurrentBuildNumber();
        } else {
            $isLoggingActive = false;
            $currentBuildNumber = '';
        }

        $oVisitor->SetMappedValue('hidden', true);
        $oVisitor->SetMappedValue('loggingActive', $isLoggingActive);
        $oVisitor->SetMappedValue('userIsAllowedToView', $isDatabaseLoggingAllowed);
        $oVisitor->SetMappedValue('sModuleSpotName', $oVisitor->GetSourceObject('sModuleSpotName'));
        $oVisitor->SetMappedValue('activeDbCounter', MigrationRecorderConstants::MIGRATION_SCRIPT_NAME);
        $oVisitor->SetMappedValue('recordingActive', $isLoggingActive);
        $oVisitor->SetMappedValue('currentUnixTimestamp', $currentBuildNumber);
    }
}
