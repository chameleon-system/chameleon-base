# Chameleon System DatabaseMigrationBundle

This bundle handles the management of Chameleon update files. It has two main function blocks

1. Search and execute updates.
   It is able to combine lists of updates. This functionality is used by the Chameleon update manager to determine
   which updates need to run and which have already been processed previously.
2. Write update files that are ready to execute.
   This recording functionality is used by the database recorder in the Chameleon backend.

Note that the migration functionality in Chameleon is considered internal and is not suited for extension or
modification by external code. Interfaces, functionality and GUI may change anytime.

## Executing Updates

An implementation of `\ChameleonSystem\DatabaseMigration\Factory\MigrationDataModelFactoryInterface` is used to collect all possible update files.

There are two implementations available:
- `\\ChameleonSystem\\DatabaseMigration\\Factory\\FileSystemMigrationDataModelFactory`: This implementation will search for updates in the file system. See 'Configuration' for details.
- `\\ChameleonSystem\\DatabaseMigrationBundle\\Bridge\\Chameleon\\MigrationDataModelFactory\\ChameleonProcessedMigrationDataModelFactory`: This implementation will collect all already processed updates from the database. Use a combination of both implementations to determine the updates that need to be run using the `\\ChameleonSystem\\DatabaseMigration\\Reducer\\MigrationDataModelReducer`.

## Configuration

The `\\ChameleonSystem\\DatabaseMigrationBundle\\Bridge\\Chameleon\\MigrationDataModelFactory\\ChameleonProcessedMigrationDataModelFactory` only needs a database connection provided by `\\ChameleonSystem\\DatabaseMigrationBundle\\Bridge\\Chameleon\\DataAccess\\MigrationDataAccess` which in return only needs the current DBAL connection to work properly.

The `\\ChameleonSystem\\DatabaseMigration\\Factory\\FileSystemMigrationDataModelFactory` needs a little more care to be able to find updates on the file system:

### Update Path Patterns

Via `\\ChameleonSystem\\DatabaseMigration\\Factory\\FileSystemMigrationDataModelFactory::addUpdatePathPattern` patterns can be added of directories inside packages or bundles that will be considered for updates. Examples:
- '/^Bridge\/Chameleon\/Migration\/Script\/.*updates/'
- '/^[^\/]*updates$/'

Note the enclosing slashes showing that those are indeed patterns, not globs!

## Creation Of Data Models

With a completely configured factory, the models can be created using `\\ChameleonSystem\\DatabaseMigration\\Factory\\MigrationDataModelFactoryInterface::createMigrationDataModels`. The result of this operation will be a map of `\\ChameleonSystem\\DatabaseMigration\\DataModel\\MigrationDataModel`s.

## Data Model Converter

To be used in the `\\TCMSUpdateManager` of Chameleon, the models need to be converted into the exchange format that can be used by the frontend javascript interface. This is done by the method `\\ChameleonSystem\\DatabaseMigrationBundle\\Bridge\\Chameleon\\Converter\\DataModelConverter::convertDataModelsToLegacySystem()`.

## Recording Updates

Inject the service `chameleon_system_database_migration.recorder.migration_recorder` and follow this call sequence:

```php
$filePointer = $migrationRecorder->startTransaction($activeTrackName, $activeBuildNumber);
$migrationRecorder->writeQueries($filePointer, $dataModels);
$migrationRecorder->endTransaction($filePointer);
```

`$activeTrackName` determines the base name of the file to be written and should be the name of the bundle the migration file is written for (e.g. "ChameleonSystemCoreBundle").

`$activeBuildNumber` is the sequential number for this update. This number will be part of the file name and is crucial for determining the order in which updates are executed on the target system. It is strongly recommended to pass a unix timestamp.

The recorder will open an update file, write the queries for the given data models, and close the file when executed in the sequence shown in the example.