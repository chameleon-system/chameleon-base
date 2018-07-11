<?php

namespace ChameleonSystem\DatabaseMigration\Constant;

class MigrationRecorderConstants
{
    const MIGRATION_SCRIPT_NAME = 'update';

    const SESSION_PARAM_MIGRATION_RECORDING_ACTIVE = 'chameleon_system_database_migration.update_recorder.active';
    const SESSION_PARAM_MIGRATION_BUILD_NUMBER = 'chameleon_system_database_migration.update_recorder.timestamp';
}
