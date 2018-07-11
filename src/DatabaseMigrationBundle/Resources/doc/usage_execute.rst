Executing Updates
=================

An implementation of `\ChameleonSystem\DatabaseMigration\Factory\MigrationDataModelFactoryInterface` is used to collect
all possible update files.

There are two implementations available:
`\\ChameleonSystem\\DatabaseMigration\\Factory\\FileSystemMigrationDataModelFactory`: This implementation will search for
updates in the file system. See 'Configuration' for details.
`\\ChameleonSystem\\DatabaseMigrationBundle\\Bridge\\Chameleon\\MigrationDataModelFactory\\ChameleonProcessedMigrationDataModelFactory`:
This implementation will collect all already processed updates from the database.
Use a combination of both implementations to determine the updates that need to be run using the
`\\ChameleonSystem\\DatabaseMigration\\Reducer\\MigrationDataModelReducer`.

Configuration
=============

The `\\ChameleonSystem\\DatabaseMigrationBundle\\Bridge\\Chameleon\\MigrationDataModelFactory\\ChameleonProcessedMigrationDataModelFactory`
only needs a database connection provided by `\\ChameleonSystem\\DatabaseMigrationBundle\\Bridge\\Chameleon\\DataAccess\\MigrationDataAccess`
which in return only needs the current DBAL connection to work properly.

The `\\ChameleonSystem\\DatabaseMigration\\Factory\\FileSystemMigrationDataModelFactory` needs a little more care to be
able to find updates on the file system:

Update Path Patterns
--------------------

Via `\\ChameleonSystem\\DatabaseMigration\\Factory\\FileSystemMigrationDataModelFactory::addUpdatePathPattern` patterns
can be added of directories inside packages or bundles that will be considered for updates.
Examples:
- '/^Bridge\/Chameleon\/Migration\/Script\/.*updates/'
- '/^[^\/]*updates$/'

Note the enclosing slashes showing that those are indeed patterns, not globs!

Creation Of Data Models
=======================

With a completely configured factory, the models can be created using
`\\ChameleonSystem\\DatabaseMigration\\Factory\\MigrationDataModelFactoryInterface::createMigrationDataModels`.
The result of this operation will be a map of `\\ChameleonSystem\\DatabaseMigration\\DataModel\\MigrationDataModel`s.

Data Model Converter
====================

To be used in the `\\TCMSUpdateManager` of Chameleon, the models need to be converted into the exchange format that can
be used by the frontend javascript interface.
This is done by the method `\\ChameleonSystem\\DatabaseMigrationBundle\\Bridge\\Chameleon\\Converter\\DataModelConverter::convertDataModelsToLegacySystem()`.
