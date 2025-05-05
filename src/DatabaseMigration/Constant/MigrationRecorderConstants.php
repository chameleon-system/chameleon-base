<?php

namespace ChameleonSystem\DatabaseMigration\Constant;

class MigrationRecorderConstants
{
    public const MIGRATION_SCRIPT_NAME = 'update';

    public const SESSION_PARAM_MIGRATION_RECORDING_ACTIVE = 'chameleon_system_database_migration.update_recorder.active';
    public const SESSION_PARAM_MIGRATION_BUILD_NUMBER = 'chameleon_system_database_migration.update_recorder.timestamp';
}
